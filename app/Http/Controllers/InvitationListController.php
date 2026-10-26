<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\InvitationMail;
use App\Models\Edition;
use App\Models\User;
use App\Models\Person;
use App\Models\InvitationList;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationListController extends Controller
{
    public function create(Edition $edition)
    {
        $user = auth()->user();

        $existingList = $this->preferredExistingList($edition, $user);
        if ($existingList !== null) {
            return redirect()->route('invitation-list-show', $existingList->id)
                ->with('success', 'Ya tienes una lista de invitaciones para esta edición. Puedes modificarla desde aquí.');
        }

        $managerPivot = $edition->managers()
            ->where('manager_id', $user->id)
            ->first()
            ?->pivot;

        $portfolios = $user->portfolios()->with('persons')->get();

        $allBrands = Person::whereNotNull('brand')->select('brand')->distinct()->orderBy('brand')->pluck('brand');

        return view('invitation_list.create', compact('edition', 'portfolios', 'managerPivot', 'allBrands'));
    }

    public function store(Request $request, Edition $edition)
    {
        $user = auth()->user();

        $existingList = $this->preferredExistingList($edition, $user);
        if ($existingList !== null) {
            return redirect()->route('invitation-list-show', $existingList->id)
                ->withErrors(['name' => 'Ya tienes una lista de invitaciones para esta edición. Solo se permite una por edición.']);
        }

        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'portfolio_id'     => ['required', 'exists:client_portfolio,id'],
            'persons'          => ['required', 'array', 'min:1'],
            'persons.*'        => ['integer', 'exists:person,id'],
            'registrations'    => ['required', 'array'],
            'registrations.*'  => ['integer', 'min:0'],
        ]);

        $portfolio = $user->portfolios()->findOrFail($validated['portfolio_id']);

        $allowedIds = $portfolio->persons()->pluck('id');
        abort_if(collect($validated['persons'])->diff($allowedIds)->isNotEmpty(), 403);

        $registrations = $this->resolveRegistrations($validated['persons'], $validated['registrations']);

        $pivot = $edition->managers()->where('manager_id', $user->id)->first()?->pivot;
        if ($pivot?->invitations_capacity !== null && $registrations->sum() > $pivot->invitations_capacity) {
            return back()
                ->withErrors(['persons' => 'Has superado tu capacidad de invitaciones para esta edición.'])
                ->withInput();
        }
        $list = InvitationList::create([
            'name'                => $validated['name'],
            'client_portfolio_id' => $portfolio->id,
            'edition_id'          => $edition->id,
        ]);
        $list->persons()->attach($this->pivotAttributes($registrations));

        return redirect()->route('invitation-list-show', $list->id)
            ->with('success', 'Lista de invitaciones creada correctamente.');
    }

    public function index(User $id)
    {
        $inv_lists = InvitationList::whereIn(
            'client_portfolio_id',
            $id->portfolios()->select('id')
        )->with(['clientPorfolio', 'edition' => fn ($query) => $query->withTrashed()->with('event')])->get();

        // A list that was sent and then superseded by an unsent copy of itself
        // (EditionController::duplicateSentInvitationLists(), triggered when its
        // edition got cancelled and reactivated) would otherwise look like a stray
        // duplicate here - same name, same portfolio, nothing in this flat table says
        // they're the same edition's two cycles. Hide it from this general listing;
        // it's still fully intact in the database and reachable from the current
        // list's own page (see show()) - this view is just not the place for it.
        $inv_lists = $this->withoutSupersededLists($inv_lists);

        return view('invitation_list.index', compact('inv_lists'));
    }

    public function show(InvitationList $list)
    {
        $list->load(['persons', 'clientPorfolio.persons', 'edition.event']);
        $currentPersonIds = $list->persons->pluck('id')->all();
        $currentRegistrations = $list->persons->pluck('pivot.allowed_registrations', 'id')->all();
        $portfolioPersons = $list->clientPorfolio->persons;

        // The other half of a cancel+reactivate cycle (see duplicateSentInvitationLists()
        // in EditionController): at most one other list shares this edition+portfolio.
        // Surfaced here so each list links to the other instead of leaving the old,
        // sent one as a dead end once it's hidden from the general listings above.
        $relatedList = $list->edition_id !== null
            ? InvitationList::where('edition_id', $list->edition_id)
                ->where('client_portfolio_id', $list->client_portfolio_id)
                ->where('id', '!=', $list->id)
                ->first()
            : null;

        return view('invitation_list.show', compact('list', 'portfolioPersons', 'currentPersonIds', 'currentRegistrations', 'relatedList'));
    }


    public function update(Request $request, InvitationList $list)
    {
        abort_if($list->isSent(), 403, 'Esta lista ya ha sido enviada y no puede modificarse.');

        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'persons'          => ['present', 'array'],
            'persons.*'        => ['integer', 'exists:person,id'],
            'registrations'    => ['array'],
            'registrations.*'  => ['integer', 'min:0'],
        ]);
        $allowedIds = $list->clientPorfolio->persons()->pluck('id');
        abort_if(collect($validated['persons'])->diff($allowedIds)->isNotEmpty(), 403);

        $registrations = $this->resolveRegistrations($validated['persons'], $validated['registrations'] ?? []);

        $capacity = $list->invitationsCapacity();
        if ($capacity !== null && $registrations->sum() > $capacity) {
            return back()
                ->withErrors(['persons' => 'Has superado tu capacidad de invitaciones para esta edición.'])
                ->withInput();
        }

        $list->update(['name' => $validated['name']]);
        $list->persons()->sync($this->pivotAttributes($registrations));

        return back()->with('success', 'Lista actualizada correctamente.');
    }

    public function sendInvitations(InvitationList $list)
    {
        abort_if($list->isSent(), 403, 'Esta lista ya ha sido enviada.');

        $list->load(['persons', 'edition.event']);
        if (($message = $this->editionUnavailableMessage($list, 'enviar las invitaciones')) !== null) {
            return back()->with('error', $message);
        }

        DB::transaction(function () use ($list) {
            foreach ($list->persons as $person) {
                $token = (string) Str::uuid();

                DB::table('invitation_list_person')
                    ->where('invitation_list_id', $list->id)
                    ->where('person_id', $person->id)
                    ->update(['token' => $token]);

                $verificationCodes = collect(range(1, $person->pivot->allowed_registrations))
                    ->map(fn () => VerificationCode::create([
                        'invitation_list_id' => $list->id,
                        'person_id'          => $person->id,
                        'edition_id'         => $list->edition_id,
                        'code'               => VerificationCode::generateUnique(),
                    ])->code);

                Mail::to($person->email)->send(
                    new InvitationMail($list->edition, $person, $token, $person->pivot->allowed_registrations, $verificationCodes)
                );
            }

            $list->sent_at = now();
            $list->save();
        });

        return back()->with('success', 'Invitaciones enviadas correctamente.');
    }

    public function resendVerificationCode(InvitationList $list, Person $person)
    {
        abort_unless($list->isSent(), 403, 'Esta lista todavía no ha sido enviada.');

        $list->load('edition.event');
        if (($message = $this->editionUnavailableMessage($list, 'reenviar el código de verificación')) !== null) {
            return back()->with('error', $message);
        }

        $pivot = DB::table('invitation_list_person')
            ->where('invitation_list_id', $list->id)
            ->where('person_id', $person->id)
            ->first();

        abort_if($pivot === null, 404, 'Esta persona no pertenece a esta lista.');

        $codes = VerificationCode::where('invitation_list_id', $list->id)
            ->where('person_id', $person->id)
            ->whereNull('used_at')
            ->pluck('code');

        if ($codes->isEmpty()) {
            return back()->with('error', 'No quedan códigos de verificación sin usar para reenviar a esta persona.');
        }

        Mail::to($person->email)->send(
            new InvitationMail($list->edition, $person, $pivot->token, $pivot->allowed_registrations, $codes)
        );

        return back()->with('success', 'Código de verificación reenviado a ' . $person->email . '.');
    }

    public function supervisedIndex(User $user)
    {
        abort_unless(auth()->id() === $user->id, 403);

        $editions = $user->managed_events()
            ->withTrashed()
            ->wherePivot('is_supervisor', true)
            // Event::event() is also a plain belongsTo on a SoftDeletes model: an
            // event whose only edition was cancelled can itself end up soft-deleted
            // (Event::hasActiveEditions() only looks at non-cancelled editions, so it
            // never blocks that deletion). Without ->withTrashed() here too, surfacing
            // cancelled editions above would resolve ->event to null for that case.
            ->with(['event' => fn ($query) => $query->withTrashed()])
            ->get()
            ->map(fn (Edition $edition) => [
                'edition'   => $edition,
                // An edition itself can be active (never cancelled) while its event
                // got soft-deleted anyway (Event::hasActiveEditions() only blocks
                // deletion for editions still in the future - a past, never-cancelled
                // edition doesn't stop it), so "cancelled" has to check both.
                'cancelled' => $edition->trashed() || ($edition->event?->trashed() ?? false),
                'managers'  => $this->managerSummaries($edition),
            ])
            // Active editions first, cancelled ones (or whose event was deleted) last.
            ->sortBy(fn (array $item) => $item['cancelled'] ? 1 : 0)
            ->values();

        return view('invitation_list.supervised-index', compact('editions'));
    }

    public function updateManagerCapacity(Request $request, Edition $edition, User $manager)
    {
        $this->authorizeSupervisor($edition);

        $pivot = $edition->managers()->where('manager_id', $manager->id)->first()?->pivot;
        abort_if($pivot === null, 404, 'Este usuario no gestiona esta edición.');

        $lists = $this->managerLists($edition, $manager);
        $errorBag = "capacity-{$edition->id}-{$manager->id}";

        abort_if(
            $lists->isNotEmpty() && $lists->every(fn (InvitationList $list) => $list->isSent()),
            403,
            'No se puede modificar: todas las listas de este gestor ya han sido enviadas.'
        );

        $validated = $request->validate([
            'invitations_capacity' => ['nullable', 'integer', 'min:0'],
        ]);

        $committed = $lists->flatMap->persons->sum('pivot.allowed_registrations');
        if ($validated['invitations_capacity'] !== null && $validated['invitations_capacity'] < $committed) {
            return back()
                ->withErrors(['invitations_capacity' => "No se puede fijar por debajo de las {$committed} invitaciones ya comprometidas."], $errorBag)
                ->withInput();
        }

        $edition->managers()->updateExistingPivot($manager->id, [
            'invitations_capacity' => $validated['invitations_capacity'],
        ]);

        return back()->with('success', 'Capacidad de invitaciones actualizada correctamente.');
    }

    private function authorizeSupervisor(Edition $edition): void
    {
        $isSupervisor = $edition->managers()
            ->where('manager_id', auth()->id())
            ->wherePivot('is_supervisor', true)
            ->exists();

        abort_unless($isSupervisor, 403);
    }

    private function preferredExistingList(Edition $edition, User $manager): ?InvitationList
    {
        // A manager can end up with more than one list for the same edition only
        // through EditionController::restore() duplicating a sent list into a fresh
        // unsent one (see docs/EditionController.md). When that happens, always
        // point back at the unsent one - it's the one that can still be edited,
        // whereas redirecting to the old sent list would look identical to being
        // permanently locked out.
        return $this->managerLists($edition, $manager)
            ->sortBy(fn (InvitationList $list) => $list->isSent() ? 1 : 0)
            ->first();
    }

    private function managerLists(Edition $edition, User $manager): \Illuminate\Support\Collection
    {
        $lists = InvitationList::where('edition_id', $edition->id)
            ->whereIn('client_portfolio_id', $manager->portfolios()->select('id'))
            ->with(['clientPorfolio', 'persons'])
            ->get();

        // Same reasoning as InvitationListController::index(): a sent list superseded
        // by its own unsent duplicate (created on reactivation) would otherwise look
        // like an unrelated duplicate wherever these lists are displayed - and would be
        // double-counted by managerSummaries()'s "committed" sum and "locked" check.
        return $this->withoutSupersededLists($lists);
    }

    /**
     * Drops any sent list that has an unsent duplicate of itself for the same
     * edition+portfolio - the historical half of a cancel+reactivate cycle (see
     * EditionController::duplicateSentInvitationLists()). Nothing is deleted; the
     * sent list is still reachable from its unsent counterpart's own page (see
     * show()). A list without such a counterpart (the common case: never cancelled,
     * or genuinely fully sent with nothing left to edit) is left untouched.
     */
    private function withoutSupersededLists(\Illuminate\Support\Collection $lists): \Illuminate\Support\Collection
    {
        return $lists->reject(fn (InvitationList $list) => $list->isSent() && $lists->contains(
            fn (InvitationList $other) => $other->id !== $list->id
                && $other->edition_id === $list->edition_id
                && $other->client_portfolio_id === $list->client_portfolio_id
                && !$other->isSent()
        ));
    }

    private function managerSummaries(Edition $edition): \Illuminate\Support\Collection
    {
        return $edition->managers->map(function (User $manager) use ($edition) {
            $lists = $this->managerLists($edition, $manager);

            return [
                'manager'   => $manager,
                'lists'     => $lists,
                'committed' => $lists->flatMap->persons->sum('pivot.allowed_registrations'),
                'locked'    => $lists->isNotEmpty() && $lists->every(fn (InvitationList $list) => $list->isSent()),
                'capacity'  => $manager->pivot->invitations_capacity,
            ];
        });
    }

    /**
     * `InvitationList::edition()` is a plain belongsTo, so once that edition is
     * cancelled (soft-deleted) $list->edition silently resolves to null even though
     * edition_id is still set - without this check, sendInvitations()/
     * resendVerificationCode() would go on to call methods on that null and blow up
     * with a raw Laravel error instead of a message the manager can act on. Returns
     * null when the list is fine to act on.
     */
    private function editionUnavailableMessage(InvitationList $list, string $action): ?string
    {
        if ($list->edition !== null) {
            return null;
        }

        return $list->edition_id === null
            ? "No se puede {$action}: esta lista no tiene ninguna edición asociada."
            : "No se puede {$action}: el evento de esta edición está cancelado.";
    }

    private function resolveRegistrations(array $personIds, array $registrations): \Illuminate\Support\Collection
    {
        return collect($personIds)->mapWithKeys(fn ($id) => [
            $id => (int) ($registrations[$id] ?? 1),
        ]);
    }

    private function pivotAttributes(\Illuminate\Support\Collection $registrations): array
    {
        return $registrations
            ->mapWithKeys(fn ($qty, $id) => [$id => ['allowed_registrations' => $qty]])
            ->all();
    }
}
