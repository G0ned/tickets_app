<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;

class EventRoutesTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;

    public function setUp():void
    {
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
        $this->actingAs($this->user);
        $response = $this->actingAs($this->user)->get(route('events-create'));
        $response->assertStatus(200);
    }

    public function test_event_creation_route_as_admin(): void
    {
        $response = $this->actingAs($this->user)->get(route('events-create'));
        $response->assertStatus(200);
    }

    public function test_event_creation_route_as_not_admin(): void
    {
        $this->user->is_admin = false;
        $response = $this->actingAs($this->user)->get(route('events-create'));
        $response->assertStatus(403);
    }

    public function test_event_creation(): void
    {
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
    {   
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
}
