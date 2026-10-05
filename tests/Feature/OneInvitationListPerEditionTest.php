<?php

namespace Tests\Feature;

use App\Enums\Type;
use App\Models\ClientPortfolio;
use App\Models\Edition;
use App\Models\Event;
use App\Models\InvitationList;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OneInvitationListPerEditionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected Edition $edition;
    protected ClientPortfolio $portfolio;
    protected Person $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'John', 'surname' => 'Doe', 'email' => 'admin@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => true, 'is_supervisor' => false,
        ]);

        $this->manager = User::create([
            'name' => 'Mister', 'surname' => 'Manager', 'email' => 'manager@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => false, 'is_supervisor' => false,
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

        $this->edition->managers()->attach($this->manager->id, [
            'is_supervisor' => false, 'is_doorman' => false, 'invitations_capacity' => 10,
        ]);

        $this->portfolio = ClientPortfolio::create([
            'name' => 'Demo Portfolio', 'user_id' => $this->manager->id,
        ]);

        $this->client = Person::create([
            'name' => 'Client', 'surname' => 'One', 'email' => 'client1@example.test',
            'phone' => '600000000', 'passport' => '22222222J', 'type' => Type::Client, 'client_portfolio_id' => $this->portfolio->id,
        ]);
    }

    private function existingList(): InvitationList
    {
        $list = InvitationList::create([
            'name' => 'Original', 'client_portfolio_id' => $this->portfolio->id,
            'edition_id' => $this->edition->id,
        ]);
        $list->persons()->attach($this->client->id, ['allowed_registrations' => 2]);

        return $list;
    }

    public function test_create_form_redirects_to_existing_list(): void
    {
        $list = $this->existingList();

        $this->actingAs($this->manager)
            ->get(route('edition-invitation-list', $this->edition->id))
            ->assertRedirect(route('invitation-list-show', $list->id));
    }

    public function test_store_is_blocked_when_manager_already_has_a_list(): void
    {
        $list = $this->existingList();

        $this->actingAs($this->manager)
            ->post(route('invitation-list-store', $this->edition->id), [
                'name' => 'Second',
                'portfolio_id' => $this->portfolio->id,
                'persons' => [$this->client->id],
                'registrations' => [$this->client->id => 1],
            ])
            ->assertRedirect(route('invitation-list-show', $list->id))
            ->assertSessionHasErrors('name');

        $this->assertSame(1, InvitationList::where('edition_id', $this->edition->id)->count());
        $this->assertDatabaseMissing('invitation_lists', ['name' => 'Second']);
    }

    public function test_manager_without_a_list_can_create_one(): void
    {
        $this->actingAs($this->manager)
            ->post(route('invitation-list-store', $this->edition->id), [
                'name' => 'First',
                'portfolio_id' => $this->portfolio->id,
                'persons' => [$this->client->id],
                'registrations' => [$this->client->id => 3],
            ])
            ->assertSessionDoesntHaveErrors();

        $list = InvitationList::where('edition_id', $this->edition->id)->sole();
        $this->assertSame(3, (int) $list->persons->first()->pivot->allowed_registrations);
    }

    public function test_existing_list_can_still_be_updated(): void
    {
        $list = $this->existingList();

        $this->actingAs($this->manager)
            ->patch(route('invitation-list-update', $list->id), [
                'name' => 'Renamed',
                'persons' => [],
                'registrations' => [],
            ])
            ->assertSessionHasNoErrors();

        $list->refresh();
        $this->assertSame('Renamed', $list->name);
        $this->assertCount(0, $list->persons);
    }

    public function test_updating_registrations_of_existing_list_works(): void
    {
        $list = $this->existingList();

        $this->actingAs($this->manager)
            ->patch(route('invitation-list-update', $list->id), [
                'name' => 'Original',
                'persons' => [$this->client->id],
                'registrations' => [$this->client->id => 5],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(5, (int) $list->persons()->first()->pivot->allowed_registrations);
    }

    public function test_other_manager_on_same_edition_can_still_create_a_list(): void
    {
        $this->existingList();

        $otherManager = User::create([
            'name' => 'Other', 'surname' => 'Manager', 'email' => 'other@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => false, 'is_supervisor' => false,
        ]);
        $this->edition->managers()->attach($otherManager->id, [
            'is_supervisor' => false, 'is_doorman' => false, 'invitations_capacity' => 10,
        ]);
        $otherPortfolio = ClientPortfolio::create(['name' => 'Other Portfolio', 'user_id' => $otherManager->id]);
        $otherClient = Person::create([
            'name' => 'Other', 'surname' => 'Client', 'email' => 'client2@example.test',
            'phone' => '600000001', 'passport' => '11111111H', 'type' => Type::Client, 'client_portfolio_id' => $otherPortfolio->id,
        ]);

        $this->actingAs($otherManager)
            ->get(route('edition-invitation-list', $this->edition->id))
            ->assertOk();

        $this->actingAs($otherManager)
            ->post(route('invitation-list-store', $this->edition->id), [
                'name' => 'Theirs',
                'portfolio_id' => $otherPortfolio->id,
                'persons' => [$otherClient->id],
                'registrations' => [$otherClient->id => 1],
            ])
            ->assertSessionDoesntHaveErrors();

        $this->assertSame(2, InvitationList::where('edition_id', $this->edition->id)->count());
    }
}
