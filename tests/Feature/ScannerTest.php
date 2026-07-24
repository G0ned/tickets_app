<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Edition;
use App\Models\Person;

class ScannerTest extends TestCase
{
    use RefreshDatabase;
    
    protected User $user;
    protected User $portero;
    protected Event $event;
    protected Edition $edition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'johndoe@example.test',
            'password' => bcrypt('johndoe1234'),
            'is_admin' => true,
            'is_supervisor' => false
        ]);

        $this->portero = User::create([
            'name' => 'Donna',
            'surname' => 'Jones',
            'email' => 'donnajones@example.test',
            'password' => bcrypt('donnajones1234'),
            'is_admin' => false,
            'is_supervisor' => false
        ]);

        $this->event = Event::create([
            'name' => 'DemoName',
            'description' => 'DemoDescription',
            'public' => false,
            'poster_path' => 'path/to/poster_file.jpeg',
            'created_by' => $this->user->id,
            'updated_by' => null,
            'deleted_by' => null
        ]);

        $this->edition = Edition::create([
            'event_id' => $this->event->id,
            'date' => '2026-08-01 09:00:00',
            'location' => 'DemoPlace',
            'duration' => 2,
            'capacity' => 50,
            'status' => false
        ]);

        $this->event->doormen()->attach($this->portero->id, [
            'is_doorman' => true,
            'is_organizer' => false
        ]);
    }

    public function test_scanner_access_admin(): void
    {
        $response = $this->actingAs($this->user)->get(route('checkin'));
        $response->assertStatus(200);
    }

    public function test_scanner_acces_doorman(): void
    {
        $response = $this->actingAs($this->portero)->get(route('checkin'));
        $response->assertStatus(200);
    }

    public function test_scanner_access_denied_other_users(): void
    {
        $other_user = User::create([
            'name' => 'Robert',
            'surname' => 'Reynolds',
            'email' => 'robertreynolds@example.test',
            'password' => bcrypt('robreyn1234'),
            'is_admin' => false,
            'is_supervisor' => false
        ]);

        $response = $this->actingAs($other_user)->get(route('checkin'));
        $response->assertStatus(403);
    }
}
