<?php

namespace Tests\Feature;

use App\Enums\Type;
use App\Models\ClientPortfolio;
use App\Models\Edition;
use App\Models\Event;
use App\Models\InvitationList;
use App\Models\Person;
use App\Models\User;
use App\Services\EditionCancellationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelledEditionInvitationListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected Edition $edition;
    protected ClientPortfolio $portfolio;
    protected Person $person;

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
            'is_supervisor' => true, 'is_doorman' => false, 'invitations_capacity' => null,
        ]);

        $this->portfolio = ClientPortfolio::create(['name' => 'Demo Portfolio', 'user_id' => $this->manager->id]);
        $this->person = Person::create([
            'name' => 'Alice', 'surname' => 'Client', 'email' => 'alice@example.test',
            'phone' => '600000000', 'passport' => '22222222J', 'type' => Type::Client,
            'client_portfolio_id' => $this->portfolio->id,
        ]);
    }

    private function sentListForCancelledEdition(): InvitationList
    {
        $list = InvitationList::create([
            'name' => 'Demo list', 'client_portfolio_id' => $this->portfolio->id,
            'edition_id' => $this->edition->id,
        ]);
        $list->sent_at = now();
        $list->save();
        $list->persons()->attach($this->person->id, ['allowed_registrations' => 1, 'token' => 'some-token']);

        app(EditionCancellationService::class)->cancel($this->edition);

        return $list;
    }

    public function test_show_page_warns_and_disables_actions_when_edition_is_cancelled(): void
    {
        $list = $this->sentListForCancelledEdition();

        $response = $this->actingAs($this->manager)->get(route('invitation-list-show', $list->id));

        $response->assertOk();
        $response->assertSee('El evento de esta edición está cancelado', false);
        $response->assertSee('disabled', false);
    }

    public function test_resending_a_code_for_a_cancelled_edition_shows_a_friendly_error_instead_of_crashing(): void
    {
        $list = $this->sentListForCancelledEdition();

        $response = $this->actingAs($this->manager)
            ->post(route('invitation-list-resend-code', ['list' => $list->id, 'person' => $this->person->id]));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('cancelado', session('error'));
    }

    public function test_sending_invitations_for_a_cancelled_edition_shows_a_friendly_error_instead_of_crashing(): void
    {
        $list = InvitationList::create([
            'name' => 'Draft list', 'client_portfolio_id' => $this->portfolio->id,
            'edition_id' => $this->edition->id,
        ]);
        $list->persons()->attach($this->person->id, ['allowed_registrations' => 1]);

        app(EditionCancellationService::class)->cancel($this->edition);

        $response = $this->actingAs($this->manager)->post(route('invitation-list-send', $list->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('cancelado', session('error'));

        $this->assertFalse($list->refresh()->isSent());
    }

    public function test_supervised_index_shows_cancelled_badge_and_still_lists_the_edition(): void
    {
        $this->sentListForCancelledEdition();

        $response = $this->actingAs($this->manager)
            ->get(route('supervised-invitations-list', $this->manager->id));

        $response->assertOk();
        $response->assertSee('Cancelada');
    }

    public function test_supervised_index_does_not_crash_when_the_event_itself_was_also_deleted(): void
    {
        $this->sentListForCancelledEdition();

        // Event::hasActiveEditions() only looks at non-cancelled editions, so an
        // event whose only edition is cancelled never blocks "Eliminar evento" -
        // the event can end up soft-deleted too, on top of the edition. The
        // ->withTrashed() on the eager-loaded 'event' relation should still
        // resolve it to the real (soft-deleted) event rather than null.
        $this->edition->event->delete();

        $response = $this->actingAs($this->manager)
            ->get(route('supervised-invitations-list', $this->manager->id));

        $response->assertOk();
        $response->assertSee('DemoName');
        $response->assertDontSee('(evento eliminado)');
    }

    public function test_supervised_index_shows_active_badge_for_a_non_cancelled_edition(): void
    {
        $this->actingAs($this->manager)
            ->get(route('supervised-invitations-list', $this->manager->id))
            ->assertOk()
            ->assertSee('Activa');
    }

    public function test_supervised_index_lists_active_editions_before_cancelled_or_event_deleted_ones(): void
    {
        // $this->edition (event "DemoName") is the active one from setUp().

        $cancelledEvent = Event::create([
            'name' => 'CancelledEventName', 'description' => 'Demo', 'public' => false,
            'poster_path' => 'path/to/poster.jpeg', 'created_by' => $this->admin->id,
            'updated_by' => null, 'deleted_by' => null,
        ]);
        $cancelledEdition = Edition::create([
            'event_id' => $cancelledEvent->id, 'date' => now()->addDays(2), 'location' => 'DemoPlace',
            'duration' => 2, 'capacity' => 50, 'status' => false,
        ]);
        $cancelledEdition->managers()->attach($this->manager->id, [
            'is_supervisor' => true, 'is_doorman' => false, 'invitations_capacity' => null,
        ]);
        app(EditionCancellationService::class)->cancel($cancelledEdition);

        // A past, never-cancelled edition whose event got deleted anyway - the
        // edition itself isn't "cancelled", but it must still sort to the back.
        $deletedEventEvent = Event::create([
            'name' => 'DeletedEventName', 'description' => 'Demo', 'public' => false,
            'poster_path' => 'path/to/poster.jpeg', 'created_by' => $this->admin->id,
            'updated_by' => null, 'deleted_by' => null,
        ]);
        $pastEdition = Edition::create([
            'event_id' => $deletedEventEvent->id, 'date' => now()->subDay(), 'location' => 'DemoPlace',
            'duration' => 2, 'capacity' => 50, 'status' => false,
        ]);
        $pastEdition->managers()->attach($this->manager->id, [
            'is_supervisor' => true, 'is_doorman' => false, 'invitations_capacity' => null,
        ]);
        $deletedEventEvent->delete();

        $response = $this->actingAs($this->manager)
            ->get(route('supervised-invitations-list', $this->manager->id));

        $response->assertOk();
        $content = $response->getContent();

        $posActive = strpos($content, 'DemoName');
        $posCancelled = strpos($content, 'CancelledEventName');
        $posDeletedEvent = strpos($content, 'DeletedEventName');

        $this->assertNotFalse($posActive);
        $this->assertNotFalse($posCancelled);
        $this->assertNotFalse($posDeletedEvent);
        $this->assertLessThan($posCancelled, $posActive, 'The active edition must be listed before the cancelled one.');
        $this->assertLessThan($posDeletedEvent, $posActive, 'The active edition must be listed before the one whose event was deleted.');

        // Both non-active cases must be flagged "Cancelada", even the one whose
        // own edition was never formally cancelled (only its event was deleted).
        $this->assertSame(2, substr_count($content, 'Cancelada'));
        $this->assertSame(1, substr_count($content, '>Activa<'));
    }
}
