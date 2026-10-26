<?php

namespace Tests\Feature;

use App\Enums\Type;
use App\Models\ClientPortfolio;
use App\Models\Edition;
use App\Models\Event;
use App\Models\InvitationList;
use App\Models\Person;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\EditionCancellationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EditionRestoreInvitationListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected Edition $edition;
    protected ClientPortfolio $portfolio;
    protected InvitationList $sentList;
    protected Person $personA;
    protected Person $personB;
    protected VerificationCode $usedCode;
    protected VerificationCode $unusedCode;

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
            'is_supervisor' => false, 'is_doorman' => false, 'invitations_capacity' => null,
        ]);

        $this->portfolio = ClientPortfolio::create(['name' => 'Demo Portfolio', 'user_id' => $this->manager->id]);
        $this->personA = Person::create([
            'name' => 'Alice', 'surname' => 'Client', 'email' => 'alice@example.test',
            'phone' => '600000000', 'passport' => '22222222J', 'type' => Type::Client,
            'client_portfolio_id' => $this->portfolio->id,
        ]);
        $this->personB = Person::create([
            'name' => 'Bob', 'surname' => 'Client', 'email' => 'bob@example.test',
            'phone' => '600000001', 'passport' => '11111111H', 'type' => Type::Client,
            'client_portfolio_id' => $this->portfolio->id,
        ]);

        $this->sentList = InvitationList::create([
            'name' => 'Original list', 'client_portfolio_id' => $this->portfolio->id,
            'edition_id' => $this->edition->id,
        ]);
        $this->sentList->sent_at = now();
        $this->sentList->save();
        $this->sentList->persons()->attach([
            $this->personA->id => ['allowed_registrations' => 2],
            $this->personB->id => ['allowed_registrations' => 3],
        ]);

        // Simulate what sendInvitations() actually leaves behind: a per-person link
        // token, one used verification code (someone already registered with it) and
        // one still-unused one (never redeemed) for personA.
        DB::table('invitation_list_person')
            ->where('invitation_list_id', $this->sentList->id)
            ->where('person_id', $this->personA->id)
            ->update(['token' => 'old-token-alice']);
        DB::table('invitation_list_person')
            ->where('invitation_list_id', $this->sentList->id)
            ->where('person_id', $this->personB->id)
            ->update(['token' => 'old-token-bob']);

        $this->usedCode = VerificationCode::create([
            'invitation_list_id' => $this->sentList->id, 'person_id' => $this->personA->id,
            'edition_id' => $this->edition->id, 'code' => 'USEDCODE',
        ]);
        $this->usedCode->update(['used_at' => now()]);

        $this->unusedCode = VerificationCode::create([
            'invitation_list_id' => $this->sentList->id, 'person_id' => $this->personA->id,
            'edition_id' => $this->edition->id, 'code' => 'FREECODE',
        ]);

        // Cancel the edition (as the state the restore feature is meant to repair).
        app(EditionCancellationService::class)->cancel($this->edition);
    }

    public function test_restoring_keeps_the_old_sent_list_untouched_and_creates_a_fresh_unsent_copy(): void
    {
        $this->actingAs($this->admin)
            ->post(route('editions-restore', $this->edition->id))
            ->assertRedirect(route('events-show', $this->edition->event_id));

        $this->sentList->refresh();
        $this->assertNotNull($this->sentList->sent_at, 'The original sent list must remain sent (historical record).');
        $this->assertCount(2, $this->sentList->persons, 'The original list must keep its original persons.');

        $newList = InvitationList::where('edition_id', $this->edition->id)
            ->where('id', '!=', $this->sentList->id)
            ->sole();

        $this->assertNull($newList->sent_at, 'The duplicated list must start unsent.');
        $this->assertSame('Original list', $newList->name);
        $this->assertSame($this->portfolio->id, $newList->client_portfolio_id);

        $newPersons = $newList->persons->keyBy('id');
        $this->assertCount(2, $newPersons, 'The duplicated list must keep the same invited clients.');
        $this->assertSame(2, (int) $newPersons[$this->personA->id]->pivot->allowed_registrations);
        $this->assertSame(3, (int) $newPersons[$this->personB->id]->pivot->allowed_registrations);
        $this->assertNull($newPersons[$this->personA->id]->pivot->token, 'The copy must not inherit the old verification token.');
        $this->assertSame(0, (int) $newPersons[$this->personA->id]->pivot->registrations_used);
    }

    public function test_new_unsent_list_can_be_edited_adding_and_removing_clients(): void
    {
        $this->actingAs($this->admin)->post(route('editions-restore', $this->edition->id));

        $newList = InvitationList::where('edition_id', $this->edition->id)
            ->where('id', '!=', $this->sentList->id)
            ->sole();

        $personC = Person::create([
            'name' => 'Carol', 'surname' => 'Client', 'email' => 'carol@example.test',
            'phone' => '600000002', 'passport' => '33333333P', 'type' => Type::Client,
            'client_portfolio_id' => $this->portfolio->id,
        ]);

        $this->actingAs($this->manager)
            ->patch(route('invitation-list-update', $newList->id), [
                'name' => 'Original list',
                'persons' => [$this->personA->id, $personC->id], // drop personB, add personC
                'registrations' => [$this->personA->id => 5, $personC->id => 1],
            ])
            ->assertSessionHasNoErrors();

        $newList->refresh();
        $ids = $newList->persons->pluck('id')->sort()->values()->all();
        $this->assertSame([$this->personA->id, $personC->id], $ids);
        $this->assertSame(5, (int) $newList->persons()->find($this->personA->id)->pivot->allowed_registrations);
    }

    public function test_manager_visiting_create_is_redirected_to_the_new_unsent_list_not_the_old_sent_one(): void
    {
        $this->actingAs($this->admin)->post(route('editions-restore', $this->edition->id));

        $newList = InvitationList::where('edition_id', $this->edition->id)
            ->where('id', '!=', $this->sentList->id)
            ->sole();

        $this->actingAs($this->manager)
            ->get(route('edition-invitation-list', $this->edition->id))
            ->assertRedirect(route('invitation-list-show', $newList->id));
    }

    public function test_restoring_invalidates_the_old_lists_unused_token_and_codes_but_keeps_used_ones(): void
    {
        $this->actingAs($this->admin)->post(route('editions-restore', $this->edition->id));

        // The unused code is gone - it can no longer be redeemed.
        $this->assertDatabaseMissing('verification_codes', ['id' => $this->unusedCode->id]);

        // The used code is untouched: it's a real historical record of who registered.
        $this->usedCode->refresh();
        $this->assertNotNull($this->usedCode->used_at);

        // Both of the old list's per-person invitation links are invalidated, not
        // just the one whose code was unused - the link itself must stop resolving
        // regardless of whether its codes had been fully consumed.
        $tokens = DB::table('invitation_list_person')
            ->where('invitation_list_id', $this->sentList->id)
            ->pluck('token', 'person_id');
        $this->assertNull($tokens[$this->personA->id]);
        $this->assertNull($tokens[$this->personB->id]);
    }

    public function test_restoring_again_does_not_duplicate_the_list_a_second_time(): void
    {
        $this->actingAs($this->admin)->post(route('editions-restore', $this->edition->id));

        $this->assertSame(2, InvitationList::where('edition_id', $this->edition->id)->count());

        // Cancel and restore a second time: the new list is still unsent, so it must
        // be left alone rather than duplicated again.
        app(EditionCancellationService::class)->cancel($this->edition->fresh());
        $this->actingAs($this->admin)->post(route('editions-restore', $this->edition->id));

        $this->assertSame(2, InvitationList::where('edition_id', $this->edition->id)->count());
    }

    public function test_manager_index_hides_the_old_sent_list_once_superseded(): void
    {
        $this->actingAs($this->admin)->post(route('editions-restore', $this->edition->id));

        $newList = InvitationList::where('edition_id', $this->edition->id)
            ->where('id', '!=', $this->sentList->id)
            ->sole();

        $response = $this->actingAs($this->manager)
            ->get(route('invitation-lists-index', $this->manager->id));

        $response->assertOk();
        $response->assertSee($this->edition->event->name);
        // Only the current, editable copy shows up here - not the superseded one.
        $this->assertSame(1, substr_count($response->getContent(), 'Original list'));
        $response->assertSee(route('invitation-list-show', $newList->id), false);
        $response->assertDontSee(route('invitation-list-show', $this->sentList->id), false);
    }

    public function test_supervised_index_hides_the_old_sent_list_too(): void
    {
        $this->edition->managers()->updateExistingPivot($this->manager->id, ['is_supervisor' => true]);

        $this->actingAs($this->admin)->post(route('editions-restore', $this->edition->id));

        $response = $this->actingAs($this->manager)
            ->get(route('supervised-invitations-list', $this->manager->id));

        $response->assertOk();
        $response->assertDontSee(route('invitation-list-show', $this->sentList->id), false);
    }

    public function test_show_page_links_each_list_to_the_other_half_of_the_cycle(): void
    {
        $this->actingAs($this->admin)->post(route('editions-restore', $this->edition->id));

        $newList = InvitationList::where('edition_id', $this->edition->id)
            ->where('id', '!=', $this->sentList->id)
            ->sole();

        // From the current (unsent) list, a link to the old, sent one.
        $this->actingAs($this->manager)
            ->get(route('invitation-list-show', $newList->id))
            ->assertOk()
            ->assertSee('Ver la lista anterior (histórico)')
            ->assertSee(route('invitation-list-show', $this->sentList->id), false);

        // And the reverse, from the old sent list back to the current one - so it's
        // never a dead end even if someone reaches it directly (an old bookmark, etc).
        $this->actingAs($this->manager)
            ->get(route('invitation-list-show', $this->sentList->id))
            ->assertOk()
            ->assertSee('Ver la lista actual')
            ->assertSee(route('invitation-list-show', $newList->id), false);
    }
}
