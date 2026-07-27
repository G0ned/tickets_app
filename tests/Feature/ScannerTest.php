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
    protected Person $attendee_1;

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
            'date' => now()->addHour(),
            'location' => 'DemoPlace',
            'duration' => 2,
            'capacity' => 50,
            'status' => false
        ]);

        $this->future_edition = Edition::create([
            'event_id' => $this->event->id,
            'date' => now()->addHours(2),
            'location' => 'DemoPlace',
            'duration' => 2,
            'capacity' => 50,
            'status' => false
        ]);

        $this->past_edition = Edition::create([
            'event_id' => $this->event->id,
            'date' => '2000-01-01 10:00:00',
            'location' => 'DemoPlace',
            'duration' => 2,
            'capacity' => 50,
            'status' => false
            ]);
        
        $this->attendee_1 = Person::create([
            'name' => 'Attendee',
            'surname' => 'Attends',
            'email' => 'attendee@mail.test',
            'phone' => '632598741',
            'passport' => '12345678Z',
            'type' => "client",
            'brand' => null,
            'client_portfolio_id' => null
        ]);

        $this->event->doormen()->attach($this->portero->id, [
            'is_doorman' => true,
            'is_organizer' => false
        ]);

        $this->edition->attendees()->attach($this->attendee_1, [
            'token' => 'test-token-1',
            'auth_for_ad' => false,
            'auth_for_comms' => false,
            'auth_image_rights' => true,
            'privacy_policy' => true,
        ]);

        $this->future_edition->attendees()->attach($this->attendee_1,[
            'token' => 'test-future-edition-token',
            'auth_for_ad' => false,
            'auth_for_comms' => false,
            'auth_image_rights' => true,
            'privacy_policy' => true,
        ]);

        $this->past_edition->attendees()->attach($this->attendee_1,[
            'token' => 'test-past-edition-token',
            'auth_for_ad' => false,
            'auth_for_comms' => false,
            'auth_image_rights' => true,
            'privacy_policy' => true,
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

    public function test_scanner_future_event(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('checkin-store'), [
            'token' => 'test-future-edition-token'
        ]);
        $response->assertStatus(404)->assertJson(['status' => 'early']);
    }

    public function test_scanner_past_event(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('checkin-store'), [
            'token' => 'test-past-edition-token'
        ]);
        $response->assertStatus(404)->assertJson(['status' => 'late']);
    }

    public function test_scanner_valid_event_date(): void
    {
        $response = $this->actingAs($this->portero)->postJson(route('checkin-store'), [
            'token' => 'test-token-1'
        ]);
        $response->assertStatus(200)->assertJson(['status' => 'success']);
    }
    
    public function test_scanner_invalid_ticket(): void
    {
        $response = $this->actingAs($this->portero)->postJson(route('checkin-store'), [
            'token' => 'test-token-2'
        ]);
        $response->assertStatus(404)->assertJson(['status' => 'error']);
    }

    public function test_scanner_ticket_already_scanned(): void
    {
        $this->actingAs($this->portero)->postJson(route('checkin-store'),
        ['token' => 'test-token-1']);
        $response =  $this->actingAs($this->portero)->postJson(route('checkin-store'),
        ['token' => 'test-token-1']);
        $response->assertStatus(200)->assertJson(['status' => 'warning']);
        
    }
}
