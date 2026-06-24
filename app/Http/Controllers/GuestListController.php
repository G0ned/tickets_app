<?php

namespace App\Http\Controllers;

use App\Models\Edition;

class GuestListController extends Controller
{
    public function create(Edition $edition)
    {
        return view('guest_list.create')->with('edition', $edition);
    }
}
