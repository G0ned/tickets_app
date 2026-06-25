<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class FormController extends Controller
{
    public function create(Event $event)
    {
        $event->load('editions');

        return view('form.create')->with('event', $event);
    }

    public function store()
    {
        //TODO
    }
}
