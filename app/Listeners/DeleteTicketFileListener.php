<?php

namespace App\Listeners;

use App\Events\AttendeeEditionCancelAssistance;
use Illuminate\Support\Facades\Storage;

class DeleteTicketFileListener
{
    /**
     * Removes the cancelled attendee's QR ticket PNG from storage. The token
     * itself already stops working the moment attendee_edition is detached
     * (CheckInController looks it up there), so this is disk cleanup, not a
     * security fix. Delete on a missing path is a safe no-op.
     */
    public function handle(AttendeeEditionCancelAssistance $event): void
    {
        if ($event->ticketToken === null) {
            return;
        }

        Storage::disk('public')->delete('tickets/' . $event->ticketToken . '.png');
    }
}
