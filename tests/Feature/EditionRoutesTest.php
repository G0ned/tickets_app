<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;

class EditionRoutesTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;
    protected Event $event;

    public function setUp(): void
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

        $this->event = Event::create([
            'name' => 'Demo Event Name',
            'description' => 'Demo Event Description',
            'public' => false,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_edition_creation_form_access(): void
    {
        $response = $this->actingAs($this->user)->get(route('editions-create', $this->event->id));
        $response->assertStatus(200);
    }

    public function test_edition_creation_post(): void
    {   
        $edition = [
            'event_id' => $this->event->id,
            'date' => '9999-12-29',
            'time' => '00:00',
            'duration' => 1,
            'location' => 'demoPlace',
            'capacity' => 999,
            'status' => false];

        $response = $this->actingAs($this->user)->post(route('editions-store', $this->event->id), $edition);

        $response->assertStatus(302);
        $this->assertDatabaseHas('editions', [
            'date' => '9999-12-29 00:00:00',
            'location' => 'demoPlace',
            'capacity' => 999,
        ]);
    }

    public function test_edition_creation_as_not_admin(): void
    {
        $not_admin = User::create([
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'johndoe@example.test',
            'password' => bcrypt('johndoe1234'),
            'is_admin' => false,
            'is_supervisor' => false
        ]);

        $response = $this->actingAs($not_admin)->get(route('editions-create', $this->event->id), [
            'event_id' => $this->event->id,
            'date' => '9999-12-29',
            'time' => '00:00',
            'duration' => 1,
            'location' => 'demoPlace',
            'capacity' => 999,
            'status' => false
        ]);
        $response->assertStatus(403);
    }

    public function test_edition_creation_no_event(): void
    {
        $response = $this->actingAs($this->user)->post(route('editions-create', 999), [
            'event_id' => $this->event->id,
            'date' => '9999-12-29',
            'time' => '00:00',
            'duration' => 1,
            'location' => 'demoPlace',
            'capacity' => 999,
            'status' => false
        ]);
        $response->assertStatus(404);
    }
}
