<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Edition;
use App\Models\User;
use App\Exports\AttendeesExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
class EditionController extends Controller
{
    public function create(Event $event)
    {
        if (!(Auth::user()->is_admin || $event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $event->id)->with('error', 'No tienes permisos para esta acción');
        }

        return view('editions.create')->with('event', $event);
    }

    public function store(Event $event)
    {
        if (!(Auth::user()->is_admin || $event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $event->id)->with('error', 'No tienes permisos para esta acción');
        }

        $validated = request()->validate([
            'location'           => ['required', 'string'],
            'duration'           => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'capacity'           => ['required', 'numeric', 'min:0'],
            'occurrences'        => ['required', 'array', 'min:1'],
            'occurrences.*.date' => ['required', 'date'],
            'occurrences.*.time' => ['required', 'date_format:H:i'],
        ]);

        // Combine each occurrence into a single datetime string, rejecting
        // duplicate date+time pairs within the same submission before ever
        // touching the database.
        $datetimes = [];
        foreach ($validated['occurrences'] as $index => $occurrence) {
            $datetime = $occurrence['date'] . ' ' . $occurrence['time'];

            if (in_array($datetime, $datetimes, true)) {
                return back()
                    ->withErrors(["occurrences.$index.date" => 'Esta fecha y hora está repetida en el formulario.'])
                    ->withInput();
            }

            $datetimes[] = $datetime;
        }

        // Same (date, location) already exists from before this submission?
        $conflicting = Edition::where('location', $validated['location'])
            ->whereIn('date', $datetimes)
            ->pluck('date');

        if ($conflicting->isNotEmpty()) {
            $formatted = $conflicting->map(fn ($d) => Carbon::parse($d)->format('d/m/Y H:i'))->join(', ');

            return back()
                ->withErrors(['occurrences' => "Ya existe una edición en {$validated['location']} para: {$formatted}."])
                ->withInput();
        }

        try {
            DB::transaction(function () use ($event, $validated, $datetimes) {
                foreach ($datetimes as $datetime) {
                    Edition::create([
                        'event_id' => $event->id,
                        'location' => $validated['location'],
                        'date'     => $datetime,
                        'duration' => $validated['duration'],
                        'capacity' => $validated['capacity'],
                        'status'   => false,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors('Error:' . $e->getMessage())->withInput();
        }

        $count = count($datetimes);

        return redirect(route('events-index'))->with('success', $count === 1
            ? 'Edición creada correctamente.'
            : "{$count} ediciones creadas correctamente.");
    }

    public function edit(Edition $edition)
    {
        if (!(Auth::user()->is_admin || $edition->event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $edition->event_id)->with('error', 'No tienes permisos para esta acción');
        }

        $edition->load(['managers', 'reminders']);
        $assignableUsers = User::whereNotIn('id', $edition->managers->pluck('id'))->get();

        return view('editions.edit', compact('edition', 'assignableUsers'));
    }

    public function update(Edition $edition)
    {
        if (!(Auth::user()->is_admin || $edition->event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $edition->event_id)->with('error', 'No tienes permisos para esta acción');
        }

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

    public function exportAttendees(Edition $edition, Request $request)
    {
        $edition->load(['event', 'attendees']);

        if ($request->query('format') === 'xlsx') {
            return Excel::download(new AttendeesExport($edition), "{$edition->event->name}-asistentes-edicion-{$edition->id}.xlsx");
        }

        return response()->streamDownload(
            function() use ($edition)
            {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Evento', 'ID edicion', 'Nombre', 'Apellidos', 'Identificación', 'e-mail', 'Teléfono',
                'Derechos para publicidad', 'Derechos para comunicaciones', 'Derechos de imagen', 'Politica de privacidad', 'Asistió', 'Hora de entrada']);
                    foreach($edition->attendees as $attendee){
                        fputcsv($handle, [$edition->event->name, $edition->id, $attendee->name, $attendee->surname, $attendee->passport, 
                        $attendee->email, $attendee->phone, $attendee->pivot->auth_for_ad ? 'Si' : 'No', $attendee->pivot->auth_for_comms ? 'Si' : 'No', 
                        $attendee->pivot->auth_image_rights ? 'Si' : 'No', $attendee->pivot->privacy_policy ? 'Si' : 'No', $attendee->pivot->attendance ? 'Si' : 'No', $attendee->pivot->checked_in_at ? \Carbon\Carbon::parse($attendee->pivot->checked_in_at)->format('d/m/Y H:i'):'-']);   
                    } 
                fclose($handle);
            }, "asistentes-{$edition->event->name}-edicion-{$edition->id}.csv", ['Content-Type' => 'text/csv']);
    }
}
