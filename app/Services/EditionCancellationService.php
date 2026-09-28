<?php

namespace App\Services;

use App\Mail\EditionCancelledMail;
use App\Mail\EventCancelledMail;
use App\Models\Edition;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Shared by EditionController (cancelling/deleting one edition) and
 * EventController (cancelling a whole event, which cancels each of its
 * editions the same way). Kept here instead of duplicated in both
 * controllers so the two flows can't drift apart.
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
     * EditionController::cancel()'s docblock for the full reasoning, which
     * applies identically here regardless of whether this was triggered by
     * cancelling a single edition or a whole event.
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

    /**
     * Emails every manager of this edition (manager_edition) that the whole
     * event was cancelled - distinct from cancel() above, which only tells
     * attendees, and from EditionRestoredMail, which is about a single
     * edition being reactivated, not the event it belongs to being cancelled.
     * Only used by EventController::cancel() - cancelling a single edition
     * doesn't notify its managers at all (see EditionController::cancel()).
     */
    public function notifyManagersOfEventCancellation(Edition $edition): void
    {
        $edition->loadMissing(['event', 'managers']);

        foreach ($edition->managers as $manager) {
            Mail::to($manager->email)->queue(new EventCancelledMail($edition, $manager));
        }
    }
}
