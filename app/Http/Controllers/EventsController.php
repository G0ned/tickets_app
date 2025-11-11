<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventsController extends Controller
{
    public function index()
    {
        //Show all active events
        $events = Event::all();
        return view('events.index', [
            'events' => $events
        ]);
    }

    public function create()
    {
        //Return event creation form
        return view('events.create');
    }

    public function store()
    {
        //Create an event
        $eventData = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'capacity' => ['required', 'integer' ]
        ]);
        $eventData['user_id'] = auth()->id();
        $eventData['is_active'] = false;
        try {
        $event = Event::create($eventData);
        return redirect('/events')->with('success', 'Evento creado correctamente');
        } catch (\Exception $e) {
            return back()->withError('No ha sido posible crear el evento.' . $e->getMessage())->withInput();
        }
    }

    public function show(Event $event)
    {
        return view('events.show', ['event' => $event]);
    }

    public function edit(Event $event)
    {
        //Return event edit form
        return view('events.edit', ['event' => $event]);
    }

    public function update()
    {
        //Update an event
        dd('To do');
    }

    public function destroy()
    {
        //Delete an event
        dd('To do');
    }
}
