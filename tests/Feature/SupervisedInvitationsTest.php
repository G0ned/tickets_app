<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Edition;
use App\Models\ClientPortfolio;
use App\Models\Person;
use App\Models\InvitationList;

class SupervisedInvitationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $supervisor;
    protected User $manager;
    protected Edition $edition;
    protected InvitationList $list;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supervisor = User::create([
            'name' => 'Sara',
            'surname' => 'Supervisor',
            'email' => 'sara@example.test',
            'password' => bcrypt('sarapass123'),
            'is_admin' => false,
            'is_supervisor' => false,
        ]);

        $this->manager = User::create([
            'name' => 'Mark',
            'surname' => 'Manager',
            'email' => 'mark@example.test',
            'password' => bcrypt('markpass123'),
            'is_admin' => false,
            'is_supervisor' => false,
        ]);

        $event = Event::create([
            'name' => 'DemoEvent',
            'description' => 'DemoDescription',
            'public' => false,
            'created_by' => $this->supervisor->id,
        ]);

        $this->edition = Edition::create([
            'event_id' => $event->id,
            'date' => '9999-12-30 00:00:00',
            'duration' => 1,
            'location' => 'demoPlace',
            'capacity' => 999,
            'status' => false,
        ]);

        $this->edition->managers()->attach($this->supervisor->id, [
            'is_supervisor' => true,
            'is_doorman' => false,
            'invitations_capacity' => null,
        ]);

        $this->edition->managers()->attach($this->manager->id, [
            'is_supervisor' => false,
            'is_doorman' => false,
            'invitations_capacity' => 10,
        ]);

        $portfolio = ClientPortfolio::create([
            'name' => 'Cartera de Mark',
            'user_id' => $this->manager->id,
        ]);

        $person1 = Person::create([
            'name' => 'Alice', 'surname' => 'A', 'passport' => 'P0001',
            'email' => 'alice@example.test', 'phone' => '600000001',
            'client_portfolio_id' => $portfolio->id,
        ]);

        $person2 = Person::create([
            'name' => 'Ben', 'surname' => 'B', 'passport' => 'P0002',
            'email' => 'ben@example.test', 'phone' => '600000002',
            'client_portfolio_id' => $portfolio->id,
        ]);

        $this->list = InvitationList::create([
            'name' => 'Lista de Mark',
            'client_portfolio_id' => $portfolio->id,
            'edition_id' => $this->edition->id,
        ]);

        $this->list->persons()->attach([
            $person1->id => ['allowed_registrations' => 3],
            $person2->id => ['allowed_registrations' => 1],
        ]);
    }

    public function test_supervisor_can_view_their_supervised_index(): void
    {
        $response = $this->actingAs($this->supervisor)
            ->get(route('supervised-invitations-list', $this->supervisor->id));

        $response->assertStatus(200);
        $response->assertSee('Mark');
        $response->assertSee('Lista de Mark');
    }

    public function test_user_cannot_view_another_users_supervised_index(): void
    {
        $response = $this->actingAs($this->manager)
            ->get(route('supervised-invitations-list', $this->supervisor->id));

        $response->assertStatus(403);
    }

    public function test_supervisor_can_raise_manager_capacity(): void
    {
        $response = $this->actingAs($this->supervisor)->patch(
            route('supervised-manager-capacity-update', [$this->edition->id, $this->manager->id]),
            ['invitations_capacity' => 20]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('manager_edition', [
            'edition_id' => $this->edition->id,
            'manager_id' => $this->manager->id,
            'invitations_capacity' => 20,
        ]);
    }

    public function test_non_supervisor_cannot_update_manager_capacity(): void
    {
        $response = $this->actingAs($this->manager)->patch(
            route('supervised-manager-capacity-update', [$this->edition->id, $this->manager->id]),
            ['invitations_capacity' => 20]
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('manager_edition', [
            'edition_id' => $this->edition->id,
            'manager_id' => $this->manager->id,
            'invitations_capacity' => 10,
        ]);
    }

    public function test_capacity_cannot_be_set_below_committed_registrations(): void
    {
        // Mark's committed registrations total 3 + 1 = 4, so 2 is below the floor.
        $response = $this->actingAs($this->supervisor)->patch(
            route('supervised-manager-capacity-update', [$this->edition->id, $this->manager->id]),
            ['invitations_capacity' => 2]
        );

        $response->assertSessionHasErrors('invitations_capacity', null, "capacity-{$this->edition->id}-{$this->manager->id}");
        $this->assertDatabaseHas('manager_edition', [
            'edition_id' => $this->edition->id,
            'manager_id' => $this->manager->id,
            'invitations_capacity' => 10,
        ]);
    }

    public function test_capacity_is_locked_once_all_manager_lists_are_sent(): void
    {
        $this->list->edition()->associate($this->edition);
        $this->list->sent_at = now();
        $this->list->save();

        $response = $this->actingAs($this->supervisor)->patch(
            route('supervised-manager-capacity-update', [$this->edition->id, $this->manager->id]),
            ['invitations_capacity' => 20]
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('manager_edition', [
            'edition_id' => $this->edition->id,
            'manager_id' => $this->manager->id,
            'invitations_capacity' => 10,
        ]);
    }
}
