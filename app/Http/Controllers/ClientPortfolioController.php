<?php

namespace App\Http\Controllers;

use App\Models\ClientPortfolio;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;

class ClientPortfolioController extends Controller
{
    public function index(User $id)
    {
        abort_unless(auth()->id() === $id->id || auth()->user()->isAdmin(), 403);

        $portfolios = $id->portfolios()->withCount('persons')->get();

        return view('portfolios.index', ['portfolios' => $portfolios, 'owner' => $id]);
    }

    public function create(User $id)
    {
        $managers = User::whereHas('managed_events')->orderBy('name')->get();
        $availablePersons = Person::with('portfolio')->orderBy('name')->get();

        return view('portfolios.create', [
            'owner'            => $id,
            'managers'         => $managers,
            'availablePersons' => $availablePersons,
        ]);
    }

    public function store(Request $request, User $id)
    {
        $validated = $request->validate([
            'user_id'      => ['required', 'integer', 'exists:users,id'],
            'name'         => ['required', 'string', 'max:255'],
            'person_ids'   => ['array'],
            'person_ids.*' => ['integer', 'exists:person,id'],
        ]);

        $owner = User::findOrFail($validated['user_id']);
        if (!$owner->managed_events()->exists()) {
            return back()
                ->withErrors(['user_id' => 'El usuario seleccionado no es gestor de ninguna edición.'])
                ->withInput();
        }

        $portfolio = ClientPortfolio::create([
            'name'    => $validated['name'],
            'user_id' => $owner->id,
        ]);

        if (!empty($validated['person_ids'])) {
            Person::whereIn('id', $validated['person_ids'])->update(['client_portfolio_id' => $portfolio->id]);
        }

        return redirect()->route('portfolios-show', $portfolio->id)->with('success', 'Cartera creada correctamente.');
    }

    public function show(ClientPortfolio $portfolio)
    {
        $portfolio->load(['persons', 'user']);

        return view('portfolios.show')->with('portfolio', $portfolio);
    }

    public function list()
    {
        $portfolio_list = ClientPortfolio::with('user')->get();
        return view ('portfolios.list')->with('portfolio_list', $portfolio_list);
    }
}
