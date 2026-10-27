<?php

namespace Tests\Feature;

use App\Enums\Type;
use App\Models\AttendeeEditionHistory;
use App\Models\Edition;
use App\Models\Event;
use App\Models\Person;
use App\Models\User;
use App\Services\EditionCancellationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContactListGuestFlagTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Edition $edition;

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
    }

    private function makePerson(string $suffix): Person
    {
        return Person::create([
            'name' => "Guest{$suffix}", 'surname' => 'Person', 'email' => "guest{$suffix}@example.test",
            'phone' => "60000000{$suffix}", 'passport' => '22222222J', 'type' => Type::Client,
        ]);
    }

    public function test_guest_under_a_cancelled_not_yet_reactivated_edition_is_still_flagged(): void
    {
        $guest = $this->makePerson('0');
        $this->edition->attendees()->attach($guest->id, [
            'token' => 'tok', 'auth_for_ad' => true, 'auth_for_comms' => true,
            'auth_image_rights' => true, 'privacy_policy' => true, 'attendance' => false,
            'is_guest' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Cancel the edition without reactivating it: the attendee_edition row
        // stays exactly where it is, but Edition itself becomes soft-deleted.
        app(EditionCancellationService::class)->cancel($this->edition);

        $response = $this->actingAs($this->admin)->get(route('contacts-index'));

        $response->assertOk();
        $people = $response->viewData('people');
        $this->assertTrue($people->firstWhere('id', $guest->id)->has_been_guest);
    }

    public function test_guest_whose_registration_was_archived_by_a_reactivation_is_still_flagged(): void
    {
        $guest = $this->makePerson('1');

        // Simulate what EditionController::restore() leaves behind: the live
        // attendee_edition row is gone, only the archived copy remains.
        AttendeeEditionHistory::create([
            'edition_id' => $this->edition->id, 'attendee_id' => $guest->id,
            'token' => 'tok', 'auth_for_ad' => true, 'auth_for_comms' => true,
            'auth_image_rights' => true, 'privacy_policy' => true, 'attendance' => false,
            'is_guest' => true, 'registered_at' => now(), 'archived_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('contacts-index'));

        $response->assertOk();
        $people = $response->viewData('people');
        $this->assertTrue($people->firstWhere('id', $guest->id)->has_been_guest);
    }

    public function test_non_guest_is_not_flagged(): void
    {
        $client = $this->makePerson('2');
        $this->edition->attendees()->attach($client->id, [
            'token' => 'tok', 'auth_for_ad' => true, 'auth_for_comms' => true,
            'auth_image_rights' => true, 'privacy_policy' => true, 'attendance' => false,
            'is_guest' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('contacts-index'));

        $people = $response->viewData('people');
        $this->assertFalse($people->firstWhere('id', $client->id)->has_been_guest);
    }
}
