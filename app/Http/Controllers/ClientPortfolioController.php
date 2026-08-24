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
        abort_unless(auth()->id() === $id->id || auth()->user()->isAdmin(), 403);

        $availablePersons = Person::with('portfolio')->orderBy('name')->get();

        return view('portfolios.create', [
            'owner'            => $id,
            'availablePersons' => $availablePersons,
        ]);
    }

    public function store(Request $request, User $id)
    {
        abort_unless(auth()->id() === $id->id || auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'person_ids'   => ['array'],
            'person_ids.*' => ['integer', 'exists:person,id'],
        ]);

        $portfolio = ClientPortfolio::create([
            'name'    => $validated['name'],
            'user_id' => $id->id,
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
