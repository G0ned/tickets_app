<?php

namespace App\Observers;

use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        //Observer de eventos que se dispara cuando se elimina un evento.
        //Se encarga de eliminar las ediciones asociadas al evento afectado.
        //Eventos y Editions tienen softDelete -> no se elimina físicamente de la BD sino que se actualiza la columna deleted_at que deja marcado
        //que el registro ha sido eliminado pero no se pierde la información.

        if($event->poster_path)
        {
            Storage::disk('public')->delete($event->poster_path);
        }
        
        $event->editions()->each(function($edition){
            $edition->delete();
        });
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }
}
