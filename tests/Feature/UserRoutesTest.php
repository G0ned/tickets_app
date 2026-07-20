<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;

class UserRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Bob',
            'surname' => 'Reynolds',
            'email' => 'bobreyn@example.test',
            'password' => bcrypt('bobreyn1234'),
            'is_admin' => true,
            'is_supervisor' => false
        ]);
    }

    public function test_user_creation_without_event_role_does_not_attach_to_any_event(): void
    {
        //Verificar que crear un usuario sin marcar organizador/portero no genera fila en event_organizer
        $this->actingAs($this->admin)->post(route('user-store'), [
            'name' => 'New',
            'surname' => 'User',
            'email' => 'newuser@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'newuser@example.test')->first();

        $this->assertNotNull($user);
        $this->assertDatabaseMissing('event_organizer', [
            'user_id' => $user->id,
        ]);
    }

    public function test_creating_user_as_organizer_attaches_to_selected_event(): void
    {
        //Verificar que al crear un usuario marcando "Organizador" y un evento, se asigna como organizador
        $event = Event::create([
            'name' => 'Demo Event Name',
            'description' => 'Demo Event Description',
            'public' => false,
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->post(route('user-store'), [
            'name' => 'New',
            'surname' => 'Organizer',
            'email' => 'neworganizer@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_organizer' => '1',
            'event_id' => $event->id,
        ]);

        $user = User::where('email', 'neworganizer@example.test')->first();

        $this->assertDatabaseHas('event_organizer', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'is_organizer' => true,
            'is_doorman' => false,
        ]);
    }

    public function test_creating_user_as_doorman_attaches_to_selected_event(): void
    {
        //Verificar que al crear un usuario marcando "Portero" y un evento, se asigna como portero
        $event = Event::create([
            'name' => 'Demo Event Name',
            'description' => 'Demo Event Description',
            'public' => false,
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->post(route('user-store'), [
            'name' => 'New',
            'surname' => 'Doorman',
            'email' => 'newdoorman@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_doorman' => '1',
            'event_id' => $event->id,
        ]);

        $user = User::where('email', 'newdoorman@example.test')->first();

        $this->assertDatabaseHas('event_organizer', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'is_organizer' => false,
            'is_doorman' => true,
        ]);
    }

    public function test_creating_organizer_without_event_fails_validation(): void
    {
        //Verificar que marcar un rol de evento sin seleccionar evento no crea el usuario
        $response = $this->actingAs($this->admin)->post(route('user-store'), [
            'name' => 'New',
            'surname' => 'Organizer',
            'email' => 'noeventorganizer@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_organizer' => '1',
        ]);

        $response->assertSessionHasErrors('event_id');
        $this->assertDatabaseMissing('users', [
            'email' => 'noeventorganizer@example.test',
        ]);
    }
}
