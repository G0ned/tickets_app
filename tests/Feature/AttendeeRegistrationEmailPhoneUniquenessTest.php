<?php

namespace Tests\Feature;

use App\Enums\Type;
use App\Models\Edition;
use App\Models\Event;
use App\Models\Person;
use App\Models\User;
use App\Services\AttendeeRegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendeeRegistrationEmailPhoneUniquenessTest extends TestCase
{
    use RefreshDatabase;

    protected Edition $edition;

    public function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'John', 'surname' => 'Doe', 'email' => 'admin@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => true, 'is_supervisor' => false,
        ]);
        $event = Event::create([
            'name' => 'DemoName', 'description' => 'DemoDescription', 'public' => true,
            'poster_path' => 'path/to/poster.jpeg', 'created_by' => $admin->id,
            'updated_by' => null, 'deleted_by' => null,
        ]);
        $this->edition = Edition::create([
            'event_id' => $event->id, 'date' => now()->addDay(), 'location' => 'DemoPlace',
            'duration' => 2, 'capacity' => 50, 'status' => false,
        ]);
    }

    private function registrationData(array $overrides = []): array
    {
        return array_merge([
            'identification' => '22222222J',
            'firstname'      => 'Alice',
            'surname'        => 'Example',
            'email'          => 'alice@example.test',
            'phone'          => '600000000',
            'img_rights_ads' => true,
            'img_rights_web' => true,
            'img_rights_rss' => true,
            'privacy_policy' => true,
        ], $overrides);
    }

    public function test_a_new_passport_with_a_brand_new_email_and_phone_registers_successfully(): void
    {
        $result = app(AttendeeRegistrationService::class)->register($this->edition, $this->registrationData());

        $this->assertArrayNotHasKey('error', $result);
        $this->assertDatabaseHas('person', ['passport' => '22222222J', 'email' => 'alice@example.test']);
    }

    public function test_a_new_passport_colliding_with_another_persons_email_is_rejected_with_a_friendly_error(): void
    {
        Person::create([
            'name' => 'Bob', 'surname' => 'Other', 'email' => 'alice@example.test',
            'phone' => '611111111', 'passport' => '11111111H', 'type' => Type::Client,
        ]);

        $result = app(AttendeeRegistrationService::class)->register(
            $this->edition,
            $this->registrationData(['identification' => '22222222J', 'phone' => '699999999'])
        );

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('email', $result['error']);
        // No crash, and no new person was created with the colliding email.
        $this->assertDatabaseMissing('person', ['passport' => '22222222J']);
    }

    public function test_a_new_passport_colliding_with_another_persons_phone_is_rejected_with_a_friendly_error(): void
    {
        Person::create([
            'name' => 'Bob', 'surname' => 'Other', 'email' => 'bob@example.test',
            'phone' => '600000000', 'passport' => '11111111H', 'type' => Type::Client,
        ]);

        $result = app(AttendeeRegistrationService::class)->register(
            $this->edition,
            $this->registrationData(['identification' => '22222222J', 'email' => 'alice-new@example.test'])
        );

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('teléfono', $result['error']);
        $this->assertDatabaseMissing('person', ['passport' => '22222222J']);
    }

    public function test_an_existing_passport_still_registers_reusing_its_stored_data_regardless_of_what_was_typed(): void
    {
        $existing = Person::create([
            'name' => 'Alice', 'surname' => 'OnFile', 'email' => 'alice.onfile@example.test',
            'phone' => '600000000', 'passport' => '22222222J', 'type' => Type::Client,
        ]);
        
        $result = app(AttendeeRegistrationService::class)->register(
            $this->edition,
            $this->registrationData(['identification' => '22222222J', 'email' => 'typed-differently@example.test'])
        );

        $this->assertArrayNotHasKey('error', $result);
        $this->assertSame($existing->id, $result['attendee']->id);
        $this->assertDatabaseHas('person', ['id' => $existing->id, 'email' => 'alice.onfile@example.test']);
    }
}
