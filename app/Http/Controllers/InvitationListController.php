<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\InvitationMail;
use App\Models\Edition;
use App\Models\User;
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

        $managerPivot = $edition->managers()
            ->where('manager_id', $user->id)
            ->first()
            ?->pivot;

        $portfolios = $user->portfolios()->with('persons')->get();

        return view('invitation_list.create', compact('edition', 'portfolios', 'managerPivot'));
    }

    public function store(Request $request, Edition $edition)
    {
        $user = auth()->user();

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

        return redirect()->route('manager-editions', $user->id)
            ->with('success', 'Lista de invitaciones creada correctamente.');
    }

    public function index(User $id)
    {
        $inv_lists = InvitationList::whereIn(
            'client_portfolio_id',
            $id->portfolios()->select('id')
        )->with('clientPorfolio')->get();

        return view('invitation_list.index', compact('inv_lists'));
    }

    public function show(InvitationList $list)
    {
        $list->load(['persons', 'clientPorfolio.persons']);
        $currentPersonIds = $list->persons->pluck('id')->all();
        $currentRegistrations = $list->persons->pluck('pivot.allowed_registrations', 'id')->all();
        $portfolioPersons = $list->clientPorfolio->persons;

        return view('invitation_list.show', compact('list', 'portfolioPersons', 'currentPersonIds', 'currentRegistrations'));
    }

    public function edit()
    {
        //TODO
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
        abort_if($list->edition === null, 422, 'La lista no tiene una edición asociada.');

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

    /**
     * Aggregated view for a supervisor: every edition where the authenticated
     * user holds the is_supervisor pivot flag, with each manager's invitation
     * lists, committed registrations, and current capacity.
     */
    public function supervisedIndex(User $user)
    {
        abort_unless(auth()->id() === $user->id, 403);

        $editions = $user->managed_events()
            ->wherePivot('is_supervisor', true)
            ->with('event')
            ->get()
            ->map(fn (Edition $edition) => [
                'edition'  => $edition,
                'managers' => $this->managerSummaries($edition),
            ]);

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

    private function managerLists(Edition $edition, User $manager): \Illuminate\Support\Collection
    {
        return InvitationList::where('edition_id', $edition->id)
            ->whereIn('client_portfolio_id', $manager->portfolios()->select('id'))
            ->with(['clientPorfolio', 'persons'])
            ->get();
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
     * Map each selected person id to its assigned number of registrations,
     * defaulting to 1 when not explicitly provided.
     */
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
