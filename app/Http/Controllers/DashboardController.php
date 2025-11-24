<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Attendee;

class DashboardController extends Controller
{
    public function index()
    {
        $events = auth()->user()->attendee->events;
        return view('attendee.dashboard', ['events' => $events, 'user' => auth()->user()]);
    }
}
