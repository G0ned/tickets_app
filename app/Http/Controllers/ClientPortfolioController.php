<?php

namespace App\Http\Controllers;

use App\Models\ClientPortfolio;
use App\Models\User;

class ClientPortfolioController extends Controller
{
    public function index(User $id)
    {
        abort_unless(auth()->id() === $id->id || auth()->user()->isAdmin(), 403);

        $portfolios = $id->portfolios()->withCount('persons')->get();

        return view('portfolios.index')->with('portfolios', $portfolios);
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
