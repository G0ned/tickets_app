<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('createdBy')->get();
        return view('events.index')->with('events', $events);
    }

    public function create()
    {
        return view('events.create');
    }

    public function show(Event $event)
    {
        $event->load(['createdBy', 'editions']);
        return view('events.details')->with('event', $event);
    }

    public function store()
    {
        $event_info = request()->validate([
            'name' => ['required', 'string'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'public' => ['required', 'boolean'],
            'desc' => ['required', 'string']
        ]);
        
        $posterPath = null;

        if(request()->hasFile('poster'))
        {
            $posterPath = request()->file('poster')->store('posters', 'public');
        }
        try{
            $new_event = Event::create([
                'name' => $event_info['name'],
                'description' => $event_info['desc'],
                'public' => $event_info['public'],
                'poster_path' => $posterPath,
                'created_by' => Auth::user()->id
            ]);
            return redirect(route('editions-create', ['event' => $new_event->id]));
        } catch(\Exception $e){
            return back()->withErrors($e->getMessage())->withInput();
        }
    }
    
     public function edit(Event $event)
    {
        $isOrganizer = $event->organizers->contains('id', Auth::id());

        if (!(Auth::user()->is_admin || $isOrganizer)) {
            return redirect()->route('events-show', $event->id)->with('error', 'No tienes permisos para esta acción');
        }

        $event->load('doormen');
        $users = User::all();
        $canManageDoormen = Auth::user()->is_admin || $isOrganizer;

        return view('events.edit')->with([
            'event' => $event,
            'users' => $users,
            'canManageDoormen' => $canManageDoormen,
        ]);
    }

    public function update(Event $event)
    {
        if (!(Auth::user()->is_admin || $event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $event->id)->with('error', 'No tienes permisos para esta acción');
        }

        $event_new_data = request()->validate([
            'name' => ['required', 'string'],
            'poster_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp|max:2048'],
            'public' => ['required', 'boolean'],
            'desc' => ['required', 'string'] 
        ]);

        $posterPath = $event->poster_path;

        if(request()->hasFile('poster_path'))
        {
            if($event->poster_path)
            {
                Storage::disk('public')->delete($posterPath);
            }
            $posterPath = request()->file('poster_path')->store('posters', 'public');
        }
        try{
            $result = $event->update([
            'name' => $event_new_data['name'],
            'public' => $event_new_data['public'],
            'description' => $event_new_data['desc'],
            'poster_path' => $posterPath,
            'updated_by' => Auth::user()->id
            ]);
            return redirect(route('events-show', $event->id))->with('success', 'Datos del evento actualizados correctamente');
        }
        catch(\Exception $e){
            return redirect(route('events-show', $event->id))->with('error', 'No se ha sido posible modificar el evento');
        }
    }

    public function assignOrganizer(Event $event)
    {
        $validated = request()->validate([
            'user_id' => ['required', 'exists:users,id']
        ]);

        $event->assignStaffRole($validated['user_id'], 'is_organizer');

        return redirect(route('events-edit', $event->id))->with('success', "Organizador asigando correctamente");
    }

    public function removeOrganizer(Event $event, User $user)
    {
        $pivot = $event->staff()->where('user_id', $user->id)->first()?->pivot;

        if ($pivot === null || !$pivot->is_organizer) {
            return redirect()->route('events-edit', $event->id)->with('error', 'Este usuario no es organizador de este evento.');
        }

        if ($pivot->is_doorman) {
            $event->staff()->updateExistingPivot($user->id, ['is_organizer' => false]);
        } else {
            $event->staff()->detach($user->id);
        }

        return redirect()->route('events-edit', $event->id)->with('success', 'Organizador eliminado correctamente');
    }

    public function assignDoorman(Event $event)
    {
        if (!(Auth::user()->is_admin || $event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-edit', $event->id)->with('error', 'No tienes permisos para esta acción');
        }

        $validated = request()->validate([
            'user_id' => ['required', 'exists:users,id']
        ]);

        $event->assignStaffRole($validated['user_id'], 'is_doorman');

        return redirect(route('events-edit', $event->id))->with('success', 'Portero asignado correctamente');
    }

    public function removeDoorman(Event $event, User $user)
    {
        $pivot = $event->staff()->where('user_id', $user->id)->first()?->pivot;

        if ($pivot === null || !$pivot->is_doorman) {
            return redirect()->route('events-edit', $event->id)->with('error', 'Este usuario no es portero de este evento.');
        }

        if ($pivot->is_organizer) {
            $event->staff()->updateExistingPivot($user->id, ['is_doorman' => false]);
        } else {
            $event->staff()->detach($user->id);
        }

        return redirect()->route('events-edit', $event->id)->with('success', 'Portero eliminado correctamente');
    }

    public function destroy(Event $event)
    {
        if($event->hasActiveEditions()){
            return back()->with('error', 'No es posible eliminar un evento con ediciones que no se han celebrado aun');
        }
        else{
            $event->delete();
            return redirect(route('events-index'));
        }
    }
}
