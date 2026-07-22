<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;

class CheckInRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Bob',
            'surname' => 'Reynolds',
            'email' => 'bobreyn@example.test',
            'password' => bcrypt('bobreyn1234'),
            'is_admin' => false,
            'is_supervisor' => false
        ]);
    }

    public function test_checkin_access_denied_for_regular_user(): void
    {
        //Verificar que un usuario sin rol de admin o portero no puede acceder al escáner
        $response = $this->actingAs($this->user)->get(route('checkin'));

        $response->assertStatus(403);
    }

    public function test_checkin_access_allowed_for_admin(): void
    {
        //Verificar que los administradores conservan el acceso al escáner
        $this->user->is_admin = true;
        $this->user->save();

        $response = $this->actingAs($this->user)->get(route('checkin'));

        $response->assertStatus(200);
    }

    public function test_checkin_access_allowed_for_doorman(): void
    {
        //Verificar que un usuario marcado como portero de un evento puede acceder al escáner
        $event = Event::create([
            'name' => 'Demo Event Name',
            'description' => 'Demo Event Description',
            'public' => false,
            'created_by' => $this->user->id,
        ]);
        $event->staff()->attach($this->user->id, ['is_organizer' => false, 'is_doorman' => true]);

        $response = $this->actingAs($this->user)->get(route('checkin'));

        $response->assertStatus(200);
    }
}
