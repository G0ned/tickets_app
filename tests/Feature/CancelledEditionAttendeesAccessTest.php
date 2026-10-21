<?php

namespace Tests\Feature;

use App\Enums\Type;
use App\Models\Edition;
use App\Models\Event;
use App\Models\Person;
use App\Models\User;
use App\Services\EditionCancellationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelledEditionAttendeesAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Edition $edition;
    protected Person $attendee;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'John', 'surname' => 'Doe', 'email' => 'admin@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => true, 'is_supervisor' => false,
        ]);

        $event = Event::create([
            'name' => 'DemoName', 'description' => 'DemoDescription', 'public' => false,
            'poster_path' => 'path/to/poster.jpeg', 'created_by' => $this->admin->id,
            'updated_by' => null, 'deleted_by' => null,
        ]);

        $this->edition = Edition::create([
            'event_id' => $event->id, 'date' => now()->addDay(), 'location' => 'DemoPlace',
            'duration' => 2, 'capacity' => 50, 'status' => false,
        ]);

        $this->attendee = Person::create([
            'name' => 'Jane', 'surname' => 'Attendee', 'email' => 'attendee@example.test',
            'phone' => '600000000', 'passport' => '22222222J', 'type' => Type::Client,
        ]);

        $this->edition->attendees()->attach($this->attendee->id, [
            'token' => 'demo-token', 'auth_for_ad' => true, 'auth_for_comms' => true,
            'auth_image_rights' => true, 'privacy_policy' => true, 'attendance' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Act: cancel the edition (soft-delete), as the "cancelled but never
        // reactivated" state the attendee-records-access feature targets.
        app(EditionCancellationService::class)->cancel($this->edition);
    }

    public function test_attendees_page_is_reachable_for_a_cancelled_edition(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('edition-attendees', $this->edition->id));

        $response->assertOk();
        $response->assertSee($this->attendee->name);
    }

    public function test_csv_export_is_reachable_for_a_cancelled_edition(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('export-attendees', ['edition' => $this->edition->id, 'format' => 'csv']));

        $response->assertOk();
    }

    public function test_events_show_links_to_attendees_for_the_cancelled_edition(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('events-show', $this->edition->event_id));

        $response->assertOk();
        $response->assertSee(route('edition-attendees', $this->edition->id), false);
    }
}
