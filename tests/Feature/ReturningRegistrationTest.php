<?php

namespace Tests\Feature;

use App\Enums\Type;
use App\Mail\AttendeeTicketMail;
use App\Models\ClientPortfolio;
use App\Models\Edition;
use App\Models\Event;
use App\Models\InvitationList;
use App\Models\Person;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReturningRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Event $event;
    protected Edition $edition;
    protected Person $returningPerson;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'John', 'surname' => 'Doe', 'email' => 'admin@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => true, 'is_supervisor' => false,
        ]);

        $this->event = Event::create([
            'name' => 'DemoName', 'description' => 'DemoDescription', 'public' => true,
            'poster_path' => 'path/to/poster.jpeg', 'created_by' => $this->admin->id,
            'updated_by' => null, 'deleted_by' => null,
        ]);
        $this->edition = Edition::create([
            'event_id' => $this->event->id, 'date' => now()->addDay(), 'location' => 'DemoPlace',
            'duration' => 2, 'capacity' => 50, 'status' => false,
        ]);

        $this->returningPerson = Person::create([
            'name' => 'Alice', 'surname' => 'OnFile', 'email' => 'alice.onfile@example.test',
            'phone' => '600000099', 'passport' => '44444444A', 'type' => Type::Client,
        ]);
    }

    private function consents(array $overrides = []): array
    {
        return array_merge([
            'registration_mode' => 'returning',
            'img_rights_ads'     => true,
            'img_rights_web'     => true,
            'img_rights_rss'     => true,
            'privacy_policy'     => true,
        ], $overrides);
    }

    public function test_public_form_returning_registrant_registers_with_only_email_and_receives_the_ticket(): void
    {
        Mail::fake();

        $response = $this->post(route('event-signup-store', $this->event->id), $this->consents([
            'editions' => $this->edition->id,
            'email'    => $this->returningPerson->email,
        ]));

        $response->assertRedirect(route('form-success'));

        $this->assertDatabaseHas('attendee_edition', [
            'edition_id'  => $this->edition->id,
            'attendee_id' => $this->returningPerson->id,
        ]);

        $this->assertSame(1, Person::where('email', $this->returningPerson->email)->count());

        Mail::assertSent(AttendeeTicketMail::class, function (AttendeeTicketMail $mail) {
            return $mail->hasTo($this->returningPerson->email);
        });
    }

    public function test_public_form_returning_registrant_with_unknown_email_is_rejected_with_a_friendly_error(): void
    {
        Mail::fake();

        $response = $this->post(route('event-signup-store', $this->event->id), $this->consents([
            'editions' => $this->edition->id,
            'email'    => 'nobody-knows-this-one@example.test',
        ]));

        $response->assertRedirect();
        $this->assertArrayHasKey('error', session('errors')['default']['messages'] ?? []);
        $this->assertDatabaseMissing('attendee_edition', ['edition_id' => $this->edition->id]);
        Mail::assertNothingSent();
    }

    public function test_public_form_returning_registrant_must_still_accept_consents_and_privacy_policy(): void
    {
        Mail::fake();

        $response = $this->post(route('event-signup-store', $this->event->id), [
            'registration_mode' => 'returning',
            'editions'          => $this->edition->id,
            'email'             => $this->returningPerson->email,
        ]);

        $response->assertSessionHasErrors(['img_rights_ads', 'img_rights_web', 'img_rights_rss', 'privacy_policy']);
        $this->assertDatabaseMissing('attendee_edition', ['edition_id' => $this->edition->id]);
        Mail::assertNothingSent();
    }

    public function test_invitation_form_returning_registrant_must_still_accept_consents_and_privacy_policy(): void
    {
        Mail::fake();

        $portfolio = ClientPortfolio::create(['name' => 'Demo Portfolio', 'user_id' => $this->admin->id]);
        $client = Person::create([
            'name' => 'Client', 'surname' => 'Three', 'email' => 'client3@example.test',
            'phone' => '600000003', 'passport' => '33333334L', 'type' => Type::Client,
            'client_portfolio_id' => $portfolio->id,
        ]);
        $list = InvitationList::create([
            'name' => 'Demo list', 'client_portfolio_id' => $portfolio->id, 'edition_id' => $this->edition->id,
        ]);
        $list->sent_at = now();
        $list->save();
        $list->persons()->attach($client->id, ['allowed_registrations' => 1, 'token' => 'guest-token-3']);
        $code = VerificationCode::create([
            'invitation_list_id' => $list->id, 'person_id' => $client->id,
            'edition_id' => $this->edition->id, 'code' => 'GUESTCD3',
        ]);

        $response = $this->post(route('invitation-registration-store', 'guest-token-3'), [
            'registration_mode' => 'returning',
            'email'             => $this->returningPerson->email,
            'verification_code' => 'GUESTCD3',
        ]);

        $response->assertSessionHasErrors(['img_rights_ads', 'img_rights_web', 'img_rights_rss', 'privacy_policy']);
        $this->assertNull($code->fresh()->used_at);
        $this->assertDatabaseMissing('attendee_edition', ['edition_id' => $this->edition->id]);
        Mail::assertNothingSent();
    }

    public function test_public_form_first_time_registrant_still_works_unchanged(): void
    {
        Mail::fake();

        $response = $this->post(route('event-signup-store', $this->event->id), $this->consents([
            'registration_mode' => 'new',
            'editions'          => $this->edition->id,
            'id_type'           => 'NIF',
            'identification'    => '22222222J',
            'firstname'         => 'Bob',
            'surname'           => 'NewPerson',
            'email'             => 'bob.new@example.test',
            'phone'             => '611111111',
            'zip_code'          => '38001',
        ]));

        $response->assertRedirect(route('form-success'));
        $this->assertDatabaseHas('person', ['passport' => '22222222J', 'email' => 'bob.new@example.test']);
        Mail::assertSent(AttendeeTicketMail::class);
    }

    public function test_invitation_form_returning_registrant_registers_with_only_email_and_verification_code(): void
    {
        Mail::fake();

        $portfolio = ClientPortfolio::create(['name' => 'Demo Portfolio', 'user_id' => $this->admin->id]);
        $client = Person::create([
            'name' => 'Client', 'surname' => 'One', 'email' => 'client@example.test',
            'phone' => '600000001', 'passport' => '11111111H', 'type' => Type::Client,
            'client_portfolio_id' => $portfolio->id,
        ]);
        $list = InvitationList::create([
            'name' => 'Demo list', 'client_portfolio_id' => $portfolio->id, 'edition_id' => $this->edition->id,
        ]);
        $list->sent_at = now();
        $list->save();
        $list->persons()->attach($client->id, ['allowed_registrations' => 1, 'token' => 'guest-token-1']);
        $code = VerificationCode::create([
            'invitation_list_id' => $list->id, 'person_id' => $client->id,
            'edition_id' => $this->edition->id, 'code' => 'GUESTCDE',
        ]);

        $response = $this->post(route('invitation-registration-store', 'guest-token-1'), $this->consents([
            'email'              => $this->returningPerson->email,
            'verification_code'  => 'GUESTCDE',
        ]));

        $response->assertRedirect(route('form-success'));

        $this->assertDatabaseHas('attendee_edition', [
            'edition_id'  => $this->edition->id,
            'attendee_id' => $this->returningPerson->id,
        ]);
        $this->assertNotNull($code->fresh()->used_at);

        Mail::assertSent(AttendeeTicketMail::class, function (AttendeeTicketMail $mail) {
            return $mail->hasTo($this->returningPerson->email);
        });
    }

    public function test_invitation_form_returning_registrant_with_unknown_email_is_rejected_and_code_stays_unused(): void
    {
        Mail::fake();

        $portfolio = ClientPortfolio::create(['name' => 'Demo Portfolio', 'user_id' => $this->admin->id]);
        $client = Person::create([
            'name' => 'Client', 'surname' => 'One', 'email' => 'client2@example.test',
            'phone' => '600000002', 'passport' => '22222223K', 'type' => Type::Client,
            'client_portfolio_id' => $portfolio->id,
        ]);
        $list = InvitationList::create([
            'name' => 'Demo list', 'client_portfolio_id' => $portfolio->id, 'edition_id' => $this->edition->id,
        ]);
        $list->sent_at = now();
        $list->save();
        $list->persons()->attach($client->id, ['allowed_registrations' => 1, 'token' => 'guest-token-2']);
        $code = VerificationCode::create([
            'invitation_list_id' => $list->id, 'person_id' => $client->id,
            'edition_id' => $this->edition->id, 'code' => 'GUESTCD2',
        ]);

        $response = $this->post(route('invitation-registration-store', 'guest-token-2'), $this->consents([
            'email'             => 'nobody-knows-this-one@example.test',
            'verification_code' => 'GUESTCD2',
        ]));

        $response->assertRedirect();
        $this->assertNull($code->fresh()->used_at);
        $this->assertDatabaseMissing('attendee_edition', ['edition_id' => $this->edition->id]);
        Mail::assertNothingSent();
    }
}
