<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Edition;

class EventRoutesTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;

    public function setUp():void
    {
        //Creacion de usuario para todos los tests
        parent::setUp();

        $this->user = User::create([
            'name' => 'Bob',
            'surname' => 'Reynolds',
            'email' => 'bobreyn@example.test',
            'password' => bcrypt('bobreyn1234'),
            'is_admin' => true,
            'is_supervisor' => false
        ]);
    }
    public function test_event_index_access_only_auth(): void
    {
        //Prueba para verificar que solo los usuarios identificados (que han hecho log-in) pueden acceder
        //a la ruta
        $this->actingAs($this->user);
        $response = $this->actingAs($this->user)->get(route('events-create'));
        $response->assertStatus(200);
    }

    public function test_event_creation_route_as_admin(): void
    {
        //Prueba para verificar que solo los usuarios administradores (user->is_admin == true) pueden acceder
        //a la ruta
        $response = $this->actingAs($this->user)->get(route('events-create'));
        $response->assertStatus(200);
    }

    public function test_event_creation_route_as_not_admin(): void
    {
        //Verificar que solos los usuarios administradores pueden acceder a la ruta que carga el formulario
        //para crear eventos nuevos
        $this->user->is_admin = false;
        $response = $this->actingAs($this->user)->get(route('events-create'));
        $response->assertStatus(403);
    }

    public function test_event_creation(): void
    {
        //Verificar que la creacion de un evento nuevo persiste los datos en la BD
        $response = $this->actingAs($this->user)->post(route('events-store'), [
            'name' => 'Demo Name',
            'desc' => 'Demo Event Description',
            'public' => false,
        ]);

        $this->assertDatabaseHas('events', [
            'name' => 'Demo Name',
        ]);
    }

    public function test_event_creation_without_user(): void
    {
        //Verificar que un evento no puede crearse si no tiene un usuario creador (user->events)
        $response = $this->post(route('events-store'), [
            'name' => 'Demo Name',
            'desc' => 'Demo Event Description',
            'public' => false,
            'poster_path' => 'example/path_to/poster.jpeg'
        ]);

        $this->assertDatabaseMissing('events', [
            'name' => 'Demo Name',
            'desc' => 'Demo Event Description'
        ]);
    }

    public function test_event_editing(): void
    {   //Verificar que el acceso a la edicion de un evento solo es posible si el usuario esta identificado
        $event = Event::create([
            'name' => 'Demo Event Name',
            'description' => 'Demo Event Description',
            'public' => false,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('events-edit', $event->id));
        $response->assertStatus(200);
    }

    public function test_event_updating(): void
    {
        //Verificar que la actualizacion de los datos de un evento persiste los cambios en la BD
        $event = Event::create([
            'name' => 'Demo Event Name',
            'description' => 'Demo Event Description',
            'public' => false,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('events-update', $event->id), [
            'name' => 'New Demo Event Name',
            'desc' => $event->description,
            'public' => false,
            'created_by' => $event->created_by
        ]);

        $this->assertDatabaseHas('events', ['name' => 'New Demo Event Name']);
    }

    public function test_event_delete(): void
    {
        //Verificar que la eliminacion de un evento lo marca como eliminado
        $event = Event::create([
            'name' => 'Demo Event Name',
            'description' => 'Demo Event Description',
            'public' => false,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('events-delete', $event->id));
        $this->assertSoftDeleted('events', [
            'id' => $event->id
        ]);
    }

    public function test_event_with_pending_editions_is_not_deleted(): void
    {
        $event = Event::create([
            'name' => 'Demo Event Name',
            'description' => 'Demo Event Description',
            'public' => false,
            'created_by' => $this->user->id,
        ]);

        $edition1 = Edition::create([
                'event_id' => $event->id,
                'date' => '2026-07-12 10:00:00',
                'duration' => 1,
                'location' => 'demoPlace',
                'capacity' => 999,
                'status' => false

            ]);

        $edition2 = Edition::create([
            'event_id' => $event->id,
            'date' => '9999-12-29 00:00:00',
            'duration' => 1,
            'location' => 'demoPlace',
            'capacity' => 999,
            'status' => false

        ]);
        $this->actingAs($this->user)->delete(route('events-delete', $event->id));

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'deleted_at' => null
        ]);
    }

    public function test_event_with_celebrated_editions_is_deleted(): void
    {
         $event = Event::create([
            'name' => 'Demo Event Name',
            'description' => 'Demo Event Description',
            'public' => false,
            'created_by' => $this->user->id,
        ]);

        $edition1 = Edition::create([
                'event_id' => $event->id,
                'date' => '2026-07-12 10:00:00',
                'duration' => 1,
                'location' => 'demoPlace',
                'capacity' => 999,
                'status' => false

            ]);

        $edition2 = Edition::create([
            'event_id' => $event->id,
            'date' => '2026-06-12 10:00:00',
            'duration' => 1,
            'location' => 'demoPlace',
            'capacity' => 999,
            'status' => false

        ]);
        $this->actingAs($this->user)->delete(route('events-delete', $event->id));

        $this->assertSoftDeleted('events', [
            'id' => $event->id
        ]);
    }
}
