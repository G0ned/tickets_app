<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Edition;
use App\Models\Person;
use App\Models\InvitationList;
use App\Models\ClientPortfolio;
class SendInvitationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $manager;
    protected Event $event;
    protected Edition $edition;
    protected InvitationList $inv_list;
    protected ClientPortfolio $portfolio;

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

        $this->manager = User::create([
            'name' => 'Mister',
            'surname' => 'Manager',
            'email' => 'misterman@example.test',
            'password' => bcrypt('Mistermanager1234'),
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

        $this->portfolio = ClientPortfolio::create([
            'name' => 'Demo Portfolio',
            'user_id' => $this->manager->id
        ]);

        $this->edition = Edition::create([
            'event_id' => $this->event->id,
            'date' => now()->addHour(),
            'location' => 'DemoPlace',
            'duration' => 2,
            'capacity' => 50,
            'status' => false
        ]);

        $this->edition->managers()->attach($this->manager->id, [
            'is_supervisor' => false,
            'is_doorman' => false,
            'invitations_capacity' => 5
        ]);

        $this->inv_list = InvitationList::create([
            'name' => 'Demo Inv List',
            'client_portfolio_id' => $this->portfolio->id,
            'edition_id' => $this->edition->id
        ]);
        
        $attendee_1 = Person::create([
            'name' => 'Attendee',
            'surname' => 'Attends',
            'email' => 'attendee@mail.test',
            'phone' => '632598741',
            'passport' => '12345678Z',
            'type' => "client",
            'brand' => null,
            'client_portfolio_id' => null
        ]);

        $attendee_2 = Person::create([
            'name' => 'Attendee',
            'surname' => 'Attends',
            'email' => 'attendee2@mail.test',
            'phone' => '632598741',
            'passport' => '16636361R',
            'type' => "client",
            'brand' => null,
            'client_portfolio_id' => null
        ]);

        $attendee_3 = Person::create([
            'name' => 'Attendee',
            'surname' => 'Attends',
            'email' => 'attendee3@mail.test',
            'phone' => '632598741',
            'passport' => '93425057T',
            'type' => "client",
            'brand' => null,
            'client_portfolio_id' => null
        ]);

        $this->inv_list->persons()->attach([
            $attendee_1->id => ['allowed_registrations' => 1],
            $attendee_2->id => ['allowed_registrations' => 5],
            $attendee_3->id => ['allowed_registrations' => 4]
        ]);
    }

    public function test_sending_invitations_to_clients(): void
    {
        $response = $this->actingAs($this->manager)->post(route('invitation-list-send', ['list' => $this->inv_list->id]));

        $response->assertRedirect();
        $this->assertDatabaseHas('invitation_lists', [
            'id' => $this->inv_list->id,
            'sent_at' => now()
        ]);
    }

    public function test_verification_codes_are_generated_when_inviation_list_sent(): void
    {
        $this->actingAs($this->manager)->post(route('invitation-list-send', ['list' => $this->inv_list->id]));

        $this->assertDatabaseHas('verification_codes', [
            'invitation_list_id' => $this->inv_list->id,
            'edition_id' => $this->edition->id
        ]);
    }
}
