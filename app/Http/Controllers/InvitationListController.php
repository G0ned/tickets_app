<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Edition;
use App\Models\InvitationList;

class InvitationListController extends Controller
{
    /**
     * Show the form for creating a new invitation list for the given edition.
     * Only the authenticated manager's own portfolios (with their persons) are exposed.
     */
    public function create(Edition $edition)
    {
        $user = auth()->user();

        // Retrieve the pivot row that links this manager to this edition.
        // We need it to know the manager's invitations_capacity limit.
        $managerPivot = $edition->managers()
            ->where('manager_id', $user->id)
            ->first()
            ?->pivot;

        // Load all portfolios belonging to this manager, eager-loading their persons
        // so we can list them in the view without N+1 queries.
        $portfolios = $user->portfolios()->with('persons')->get();

        return view('invitation_list.create', compact('edition', 'portfolios', 'managerPivot'));
    }

    /**
     * Persist a new invitation list and attach the selected persons to it.
     */
    public function store(Request $request, Edition $edition)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            // The hidden input sends which of the manager's portfolios was active.
            'portfolio_id' => ['required', 'exists:client_portfolio,id'],
            // At least one person must be selected.
            'persons'      => ['required', 'array', 'min:1'],
            'persons.*'    => ['integer', 'exists:person,id'],
        ]);

        // Verify the submitted portfolio actually belongs to this manager.
        // findOrFail throws 404 if the portfolio doesn't belong to the user's scope.
        $portfolio = $user->portfolios()->findOrFail($validated['portfolio_id']);

        // Authorization: make sure every selected person_id belongs to that portfolio.
        // Any ID not in $allowedIds means a tampered request — abort with 403.
        $allowedIds = $portfolio->persons()->pluck('id');
        abort_if(collect($validated['persons'])->diff($allowedIds)->isNotEmpty(), 403);

        // Check the manager's invitation capacity for this edition.
        // $pivot->invitations_capacity is nullable: null means no limit.
        $pivot = $edition->managers()->where('manager_id', $user->id)->first()?->pivot;
        if ($pivot?->invitations_capacity !== null && count($validated['persons']) > $pivot->invitations_capacity) {
            return back()
                ->withErrors(['persons' => 'Has superado tu capacidad de invitaciones para esta edición.'])
                ->withInput();
        }

        // Create the list and attach the selected persons via the pivot table.
        $list = InvitationList::create([
            'name'                => $validated['name'],
            'client_portfolio_id' => $portfolio->id,
        ]);
        $list->persons()->attach($validated['persons']);

        return redirect()->route('manager-editions', $user->id)
            ->with('success', 'Lista de invitaciones creada correctamente.');
    }

    public function show(InvitationList $list)
    {
        //TODO
    }

    public function edit()
    {
        //TODO
    }

    public function update()
    {
        //TODO
    }
}
