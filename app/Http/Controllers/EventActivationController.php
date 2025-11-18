<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventActivationController extends Controller
{
    public function activate($eventId)
    {
        //Buscar en la BD el evento con el ID proporcionado y guardarlo en una variable.
        $event = Event::find($eventId);
        //El campo is_active del evento se establece a true
        $event->is_active = true;
        //Se guarda el cambio realizado en la BD
        $event->save();
        //Se redirige al usuario a la página de detalles del mismo evento.
        return redirect()->route('events.show', ['event' => $eventId])->with('success', 'Evento activado.');
    }

    public function deactivate($eventId)
    {
        $event = Event::find($evendId);
        $event->is_active = false;
        $event->save();
        return redirect()->route('events.show', ['event' => $eventId])->with('success', 'Evento desactivado.');
    }
}
