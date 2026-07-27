<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Person;
use App\Models\Event;
use App\Models\Edition;
use App\Models\User;
use App\Models\InvitationList;
use App\Models\ClientPortfolio;
use App\Models\VerificationCode;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Person $person;
    protected Event $event;
    protected Edition $edition;
    protected ClientPortfolio $portfolio;
    protected InvitationList $inv_list;
    protected VerificationCode $ver_code;

    public function setUp(): void
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

        $this->portfolio = ClientPortfolio::create([
            'name' => 'DemoPortfolio',
            'user_id' => $this->user->id
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

        $this->person = Person::create([
            'name' => 'Attendee',
            'surname' => 'Attends',
            'email' => 'attendee@mail.test',
            'phone' => '632598741',
            'passport' => '12345678Z',
            'type' => "client",
            'brand' => null,
            'client_portfolio_id' => $this->portfolio->id
        ]);

        $this->inv_list = InvitationList::create([
            'name' => 'Demo Inv List',
            'client_portfolio_id' => $this->portfolio->id,
            'edition_id' => $this->edition->id
        ]);
        $this->inv_list->sent_at = now();
        $this->inv_list->save();

        $this->ver_code = VerificationCode::create([
            'invitation_list_id' => $this->inv_list->id,
            'person_id' => $this->person->id,
            'edition_id' => $this->edition->id,
            'code' => 'ExampCde',
            'used_at' => null,
        ]);

        $this->inv_list->persons()->attach($this->person, [
            'allowed_registrations' => 2,
            'token' => 'test-token-1',
            'registrations_used' => 0
        ]);
    }

    public function test_form_access(): void
    {
        $response = $this->get(route('invitation-registration-create', ['token' => 'test-token-1']));
        $response->assertStatus(200);
    }

    public function test_form_sign_up(): void
    {
        $response = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']), [
            'firstname' => 'Attendee',
            'surname' => 'Attends',
            'email' => 'attendee@mail.test',
            'phone' => '632598741',
            'id_type' => 'NIF',
            'identification' => '12345678Z',
            'zip_code' => '00000',
            'img_rights_ads' => false,
            'img_rights_web' => true,
            'img_rights_rss' => true,
            'privacy_policy' => true,
            'verification_code' => 'ExampCde'
        ]);

        $response->assertRedirect(route('form-success'));
    }

    public function test_form_sign_up_wrong_verification_code(): void
    {
        $response = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']), [
            'firstname' => 'Attendee',
            'surname' => 'Attends',
            'email' => 'attendee@mail.test',
            'phone' => '632598741',
            'id_type' => 'NIF',
            'identification' => '12345678Z',
            'zip_code' => '00000',
            'img_rights_ads' => false,
            'img_rights_web' => true,
            'img_rights_rss' => true,
            'privacy_policy' => true,
            'verification_code' => 'cdeexamp'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseMissing('attendee_edition', [
            'edition_id' => $this->edition->id,
            'attendee_id' => $this->person->id,
        ]);
    }
}
