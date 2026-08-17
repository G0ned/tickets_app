<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\EditionReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditionReminderController extends Controller
{
    public function store(Request $request, Edition $edition)
    {
        if (!(Auth::user()->is_admin || $edition->event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $edition->event_id)->with('error', 'No tienes permisos para esta acción');
        }

        $validated = $request->validate([
            'days_before' => ['required', 'integer', 'min:1', 'max:365'],
        ], [
            'days_before.required' => 'Debe indicar la antelación del recordatorio, en días.',
            'days_before.integer'  => 'La antelación debe ser un número entero de días.',
            'days_before.min'      => 'La antelación mínima es de :min día.',
            'days_before.max'      => 'La antelación máxima es de :max días.',
        ]);

        if ($edition->reminders()->where('days_before', $validated['days_before'])->exists()) {
            return back()
                ->withErrors(['days_before' => 'Ya existe un recordatorio para esta edición con esa antelación.'])
                ->withInput();
        }

        $edition->reminders()->create([
            'days_before' => $validated['days_before'],
            'created_by'  => Auth::id(),
        ]);

        return redirect()->route('editions-edit', $edition->id)->with('success', 'Recordatorio programado correctamente.');
    }

    public function destroy(Edition $edition, EditionReminder $reminder)
    {
        if (!(Auth::user()->is_admin || $edition->event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $edition->event_id)->with('error', 'No tienes permisos para esta acción');
        }

        abort_unless($reminder->edition_id === $edition->id, 404);

        $reminder->delete();

        return redirect()->route('editions-edit', $edition->id)->with('success', 'Recordatorio eliminado correctamente.');
    }
}
