<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AttendeSignUpEvent;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class GenerateTicketListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AttendeSignUpEvent $event): void
    {
        $fileName = "/tickets/ticket_event_{$event->event->id}_attendee_{$event->attendee->user->firstname}_{$event->attendee->user->surname}.svg";
        $qrImage = QrCode::format('svg')
                    ->size(300)
                    ->generate($event->jsonPayLoad);
        Storage::disk('public')->put($fileName, $qrImage);
    }
}
