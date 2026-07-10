<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientPortfolio;

class ClientPortfolioController extends Controller
{
    public function index(Request $request)
    {
        $portfolios = $request->user()->portfolios()->withCount('persons')->get();

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
