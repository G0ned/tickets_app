<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Edition;
use App\Models\User;
class EditionController extends Controller
{
    public function create(Event $event) 
    {
        return view('editions.create')->with('event', $event);
    }

    public function store(Event $event)
    {
        $edition_info = request()->validate([
            'location' => ['required', 'string'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'capacity' => ['required', 'numeric', 'min:0'],
        ]);
        $edition_info['status'] = false;
        try{
            $new_edition = Edition::create([
                'event_id' => $event->id,
                'location' => $edition_info['location'],
                'date' => $edition_info['date'] . " " . $edition_info['time'],
                'duration' => $edition_info['duration'],
                'capacity' => $edition_info['capacity'],
                'status' => $edition_info['status']
            ]);
            return redirect(route('events-index'));
        }
        catch(\Exception $e){
            return back()->withErrors('Error:' . $e->getMessage());
        }
    }

    public function edit(Edition $edition)
    {
        $edition->load('managers');
        $isManager = $edition->managers->contains('id', auth()->id());
        $assignableUsers = User::whereNotIn('id', $edition->managers->pluck('id'))->get();

        return view('editions.edit', compact('edition', 'isManager', 'assignableUsers'));
    }

    public function update(Edition $edition)
    {
        $validated = request()->validate([
            'location' => ['required', 'string'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'capacity' => ['required', 'numeric', 'min:0'], 

        ]);

        try
        {
            $edition->update([
                'location' => $validated['location'],
                'date' => $validated['date'] . " " . $validated['time'],
                'duration' => $validated['duration'],
                'capacity' => $validated['capacity'],
            ]);
            return redirect(route('events-index'));
        }
        catch(\Exception $e)
        {
            return back()->withErrors('Error: '. $e->getMessage()); 
        }
        
    }

    public function destroy(Edition $edition)
    {
        $edition->delete();
        return redirect(route('events-index'));
    }

    public function assignManager(Request $request, Edition $edition)
    {
        $validated = $request->validate([
            'user_id'              => ['required', 'exists:users,id'],
            'is_supervisor'        => ['boolean'],
            'is_doorman'           => ['boolean'],
            'invitations_capacity' => ['nullable', 'integer', 'min:0'],
        ]);

        $edition->managers()->attach($validated['user_id'], [
            'is_supervisor'        => $validated['is_supervisor'] ?? false,
            'is_doorman'           => $validated['is_doorman'] ?? false,
            'invitations_capacity' => $validated['invitations_capacity'] ?? null,
        ]);

        return redirect()->route('editions-edit', $edition->id)
            ->with('success', 'Gestor asignado correctamente.');
    }

    public function managerEditions(User $user)
    {
        $user_editions = $user->managed_events()->with('event')->get();
        return view('editions.manager-editions')->with('editions', $user_editions);
    }

    public function attendees(Edition $edition)
    {
        $edition->load(['event', 'attendees']);

        return view('editions.attendees', compact('edition'));
    }

    public function exportAttendees(Edition $edition)
    {
        $edition->load(['event', 'attendees']);
           
    }

}
