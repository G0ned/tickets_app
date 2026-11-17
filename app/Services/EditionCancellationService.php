<?php

namespace App\Services;

use App\Mail\EditionCancelledMail;
use App\Mail\EditionCancelledManagerMail;
use App\Models\Edition;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EditionCancellationService
{
    /**
     * Cancela la celebración de una edición de manera que hace un soft-delete
     * (para poder recuperarla en el futuro en caso de ser necesario). A la vez
     * Elimina los tickets de cada uno de los asistentes registrados y se envía
     * un correo a cada uno de ellos indicándoles que se ha cancelado la edición.
     * También se avisa por correo a los gestores de la edición.
     */
    public function cancel(Edition $edition): void
    {
        $edition->load(['event', 'attendees.portfolio.user', 'managers']);
        $attendees = $edition->attendees;
        $managers = $edition->managers;

        $edition->delete();

        foreach ($attendees as $attendee) {
            if ($attendee->pivot->token) {
                Storage::disk('public')->delete('tickets/' . $attendee->pivot->token . '.png');
            }

            Mail::to($attendee->email)->queue(new EditionCancelledMail($edition, $attendee));
        }

        foreach ($managers as $manager) {
            Mail::to($manager->email)->queue(new EditionCancelledManagerMail($edition, $manager));
        }
    }
}
