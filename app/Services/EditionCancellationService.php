<?php

namespace App\Services;

use App\Mail\EditionCancelledMail;
use App\Models\Edition;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Shared by EditionController::cancel() and destroy() (cancelling one
 * edition, whether from its own edit page or via the "Cancelar evento"
 * button on the parent event's page - see EventController::show()'s
 * $pendingEdition). Kept here instead of duplicated in both action methods
 * so they can't drift apart.
 */
class EditionCancellationService
{
    /**
     * Cancels the celebration of one edition: soft-deletes it (Edition uses
     * SoftDeletes, so it can be restored later), deletes the ticket PNG of
     * each currently-registered attendee, and emails each of them.
     *
     * Deliberately does NOT touch attendee_edition.cancelled_at (reserved for
     * an attendee's own manual cancellation) or manager_edition - see
     * EditionController::cancel()'s docblock for the full reasoning.
     */
    public function cancel(Edition $edition): void
    {
        $edition->load(['event', 'attendees']);
        $attendees = $edition->attendees;

        $edition->delete();

        foreach ($attendees as $attendee) {
            if ($attendee->pivot->token) {
                Storage::disk('public')->delete('tickets/' . $attendee->pivot->token . '.png');
            }

            Mail::to($attendee->email)->queue(new EditionCancelledMail($edition, $attendee));
        }
    }
}
