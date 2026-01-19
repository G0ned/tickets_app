<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Attendee;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->getRole() === 'admin') {
            $events = auth()->user()->events;
            if (!$events) {
                $events = [];
            }
            return view('admin.dashboard', ['events' => $events, 'user' => auth()->user()]);
        }
        $events = auth()->user()->attendee->events;
        return view('attendee.dashboard', ['events' => $events, 'user' => auth()->user()]);
    }
}
