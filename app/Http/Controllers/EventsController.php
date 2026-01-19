<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventsController extends Controller
{
    public function index()
    {
        //Show all active events
        if (auth()->user() && auth()->user()->getRole() == 'admin') {
            $events = Event::all();
        } else {
            $events = Event::where('is_active', '=', true)->get();
        }
        return view('events.index', ['events' => $events]);
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
            'time' => ['required', 'date_format:H:i'],
            'capacity' => ['required', 'integer' ]
        ]);
        $eventData['user_id'] = auth()->id();
        $eventData['is_active'] = false;
        $eventData['date'] = $eventData['date'] . " " . $eventData['time'];
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

    public function showActive()
    {
        $active_events = Event::where('is_active', '=', true)->get(); //whereDate('date', '<', Carbon::now()->toDateString())
        return view('events.active', ['events' => $active_events]);
    }
    
    public function edit(Event $event)
    {
        //Return event edit form
        return view('events.edit', ['event' => $event]);
    }

    public function update(Event $event)
    {
        //Update an event
        $eventData = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'capacity' => ['required', 'integer' ]
        ]);
        try {
            $event->update($eventData);
            return redirect('/events')->with('success', 'Evento actualizado correctamente');
        } catch (\Exception $e) {
            return back()->withError('No ha sido posible actualizar el evento.' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Event $event)
    {
        //Delete an event
        $event->delete();
        return redirect('/events')->with('success', 'Evento eliminado correctamente');
    }
}
