<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Edition;
use App\Models\Person;

class TimezoneCheckInTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_timezone_is_atlantic_canary(): void
    {
        $this->assertSame('Atlantic/Canary', config('app.timezone'));
        $this->assertSame('Atlantic/Canary', date_default_timezone_get());
        $this->assertSame('Atlantic/Canary', now()->timezoneName);

        $this->assertContains(now()->utcOffset(), [0, 60]);
    }

    public function test_scanning_a_ticket_within_the_one_hour_window_succeeds(): void
    {
        $admin = User::create([
            'name' => 'Admin', 'surname' => 'Istrator', 'email' => 'admin@example.test',
            'password' => bcrypt('password123'), 'is_admin' => true, 'is_supervisor' => false,
        ]);

        $event = Event::create([
            'name' => 'DemoEvent', 'description' => 'DemoDescription', 'public' => false,
            'poster_path' => null, 'created_by' => $admin->id,
        ]);

        $edition = Edition::create([
            'event_id' => $event->id, 'date' => now()->addMinutes(59),
            'location' => 'DemoPlace', 'duration' => 2, 'capacity' => 50, 'status' => false,
        ]);

        $attendee = Person::create([
            'name' => 'Attendee', 'surname' => 'Attends', 'email' => 'attendee@example.test',
            'phone' => '600000001', 'passport' => '12345678Z', 'type' => 'client', 'brand' => null,
        ]);

        $edition->attendees()->attach($attendee->id, [
            'token' => 'canary-window-token',
            'auth_for_ad' => false, 'auth_for_comms' => false,
            'auth_image_rights' => true, 'privacy_policy' => true,
        ]);

        $this->assertTrue(now()->addHour()->greaterThanOrEqualTo($edition->date));
        $this->assertFalse($edition->hasEnded());

        $response = $this->actingAs($admin)->postJson(route('checkin-store'), [
            'token' => 'canary-window-token',
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('attendee_edition', [
            'edition_id' => $edition->id, 'attendee_id' => $attendee->id, 'attendance' => true,
        ]);
    }
}
