<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

        $this->other_edition = Edition::create([
            'event_id' => $this->event->id,
            'date' => now()->addHour(),
            'location' => 'PlaceDemo',
            'duration' => 3,
            'capacity' => 80,
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

        $this->inv_list_2 = InvitationList::create([
            'name' => 'Demo Another Inv List',
            'client_portfolio_id' => $this->portfolio->id,
            'edition_id' => $this->other_edition->id
        ]);

        $this->ver_code = VerificationCode::create([
            'invitation_list_id' => $this->inv_list->id,
            'person_id' => $this->person->id,
            'edition_id' => $this->edition->id,
            'code' => 'ExampCde',
            'used_at' => null,
        ]);

        $this->ver_code_2 = VerificationCode::create([
            'invitation_list_id' => $this->inv_list_2->id,
            'person_id' => $this->person->id,
            'edition_id' => $this->other_edition->id,
            'code' => 'ExampCde2',
            'used_at' => null
        ]);

        $this->inv_list->persons()->attach($this->person, [
            'allowed_registrations' => 2,
            'token' => 'test-token-1',
            'registrations_used' => 0
        ]);

        $this->inv_list_2->persons()->attach($this->person, [
            'allowed_registrations' => 2,
            'token' => 'test-token-2',
            'registrations_used' => 0
        ]);
    }

    public function test_form_access(): void
    {
        $response = $this->get(route('invitation-registration-create', ['token' => 'test-token-1']));
        $response->assertStatus(200);
    }

    public function test_form_sign_up_non_existent_token(): void
    {
        $response = $this->get(route('invitation-registration-create', ['token' => 'non-existent-token']));
        $response->assertStatus(404);
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

    public function test_duplicate_registration(): void
    {
        $second_code = VerificationCode::create([
            'invitation_list_id' => $this->inv_list->id,
            'person_id' => $this->person->id,
            'edition_id' => $this->edition->id,
            'code' => 'ExampCde3',
            'used_at' => null,
        ]);

        $first_sign_up = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']), [
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

        $second_sign_up = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']), [
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
            'verification_code' => 'ExampCde3'
        ]);

        $second_sign_up->assertRedirect();
        $this->assertArrayHasKey('error', session('errors')['default']['messages']??[]);
        $this->assertDatabaseCount('attendee_edition', 1);
        $this->assertNull($second_code->fresh()->used_at);
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

    public function test_sign_up_not_allowed_expired_verification_code(): void
    {
        $this->ver_code->used_at = now();
        $this->ver_code->save();
        $response = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']),[
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
        $response->assertRedirect();
        $this->assertArrayHasKey('error', session('errors')['default']['messages']??[]);
        $this->assertDatabaseMissing('attendee_edition', [
            'edition_id' => $this->edition->id,
            'attendee_id' => $this->person->id
        ]);
    }

    public function test_sign_up_not_allowed_if_verification_code_is_from_other_invitation(): void
    {
        $response = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']),
        [   'firstname' => 'Attendee',
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
            'verification_code' => 'ExampCde2'
        ]);
        $response->assertRedirect();
        $this->assertArrayHasKey('error', session('errors')['default']['messages']??[]);
        $this->assertDatabaseMissing('attendee_edition', [
            'edition_id' => $this->edition->id,
            'attendee_id' => $this->person->id,
        ]);
    }

    public function test_link_is_reusable_after_previous_sign_ups(): void
    {
        $first_sign_up = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']),[
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
            'privacy_policy' => false,
            'verification_code' => 'ExampCde'
        ]);

        $response = $this->get(route('invitation-registration-create', ['token' => 'test-token-1']));
        $response->assertStatus(200);
    }

    public function test_sign_up_is_allowed_after_previous_sign_up(): void
    {
        $second_code = VerificationCode::create([
            'invitation_list_id' => $this->inv_list->id,
            'person_id' => $this->person->id,
            'edition_id' => $this->edition->id,
            'code' => 'ExampCde3',
            'used_at' => null,
        ]);

        $first_sign_up = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']),[
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
        $first_sign_up->assertRedirect(route('form-success'));

        $second_sign_up = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']),[
            'firstname' => 'Second',
            'surname' => 'Attendee',
            'email' => 'second_attendee@mail.test',
            'phone' => '658993210',
            'id_type' => 'NIF',
            'identification' => '96918757T',
            'zip_code' => '00000',
            'img_rights_ads' => false,
            'img_rights_web' => true,
            'img_rights_rss' => true,
            'privacy_policy' => true,
            'verification_code' => $second_code->code
            ]);

        $second_sign_up->assertRedirect(route('form-success'));

        $second_attendee = Person::where('passport', '96918757T')->first();
        $this->assertNotNull($second_attendee);
        $this->assertDatabaseHas('attendee_edition', [
            'edition_id' => $this->edition->id,
            'attendee_id' => $second_attendee->id,
        ]);
    }

    public function test_form_sign_up_not_allowed_if_not_privacy_policy(): void
    {
        $response = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']),[
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
            'privacy_policy' => false,
            'verification_code' => 'ExampCde'
        ]);

        $response->assertRedirect();
        $this->assertArrayHasKey('privacy_policy', session('errors')['default']['messages']??[]);
        $this->assertDatabaseMissing('attendee_edition', [
            'edition_id' => $this->edition->id,
            'attendee_id' => $this->person->id
        ]);
    }

    public function test_sign_up_not_allowed_when_no_capacity(): void
    {
        $this->edition->capacity = 0;
        $this->edition->save();
        $response = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']),[
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
        $response->assertRedirect();
        $this->assertArrayHasKey('error', session('errors')['default']['messages']??[]);
        $this->assertDatabaseMissing('attendee_edition', [
            'edition_id' => $this->edition->id,
            'attendee_id' => $this->person->id
        ]);
    }

    public function test_form_is_not_loaded_with_no_invitations_left(): void
    {
        DB::table('invitation_list_person')
        ->where('invitation_list_id', $this->inv_list->id)
        ->where('person_id', $this->person->id)
        ->update(['registrations_used'=> 2]);

        $response = $this->get(route('invitation-registration-create', ['token' => 'test-token-1']));

        $response->assertStatus(200);
        $response->assertViewIs('invitation.unavailable');
    }

    public function test_sign_up_form_with_no_invitations_left(): void
    {
        DB::table('invitation_list_person')
        ->where('invitation_list_id', $this->inv_list->id)
        ->where('person_id', $this->person->id)
        ->update(['registrations_used'=> 2]);

        $response = $this->post(route('invitation-registration-store', ['token' => 'test-token-1']),[
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
        $response->assertStatus(200);
        $response->assertViewIs('invitation.unavailable');
        $this->assertDatabaseMissing('attendee_edition', [
            'edition_id' => $this->edition->id,
            'attendee_id' => $this->person->id
        ]);
    }
}
