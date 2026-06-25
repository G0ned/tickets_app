<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Edition;
use App\Models\User;
use App\Models\InvitationList;

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
            'name'         => ['required', 'string', 'max:255'],
            'portfolio_id' => ['required', 'exists:client_portfolio,id'],
            'persons'      => ['required', 'array', 'min:1'],
            'persons.*'    => ['integer', 'exists:person,id'],
        ]);

        $portfolio = $user->portfolios()->findOrFail($validated['portfolio_id']);

        $allowedIds = $portfolio->persons()->pluck('id');
        abort_if(collect($validated['persons'])->diff($allowedIds)->isNotEmpty(), 403);
        $pivot = $edition->managers()->where('manager_id', $user->id)->first()?->pivot;
        if ($pivot?->invitations_capacity !== null && count($validated['persons']) > $pivot->invitations_capacity) {
            return back()
                ->withErrors(['persons' => 'Has superado tu capacidad de invitaciones para esta edición.'])
                ->withInput();
        }
        $list = InvitationList::create([
            'name'                => $validated['name'],
            'client_portfolio_id' => $portfolio->id,
        ]);
        $list->persons()->attach($validated['persons']);

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
        $portfolioPersons = $list->clientPorfolio->persons;

        return view('invitation_list.show', compact('list', 'portfolioPersons', 'currentPersonIds'));
    }

    public function edit()
    {
        //TODO
    }

    public function update(Request $request, InvitationList $list)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'persons'   => ['present', 'array'],
            'persons.*' => ['integer', 'exists:person,id'],
        ]);
        $allowedIds = $list->clientPorfolio->persons()->pluck('id');
        abort_if(collect($validated['persons'])->diff($allowedIds)->isNotEmpty(), 403);

        $list->update(['name' => $validated['name']]);
        $list->persons()->sync($validated['persons']);

        return back()->with('success', 'Lista actualizada correctamente.');
    }
}
