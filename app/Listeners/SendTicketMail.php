<?php

namespace App\Listeners;

use App\Events\AttendeeEditionSignUpEvent;
use App\Mail\AttendeeTicketMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendTicketMail
{
    use InteractsWithQueue;

    public $tries = 3;

    /**
     * Handle the event.
     */
    public function handle(AttendeeEditionSignUpEvent $event): void
    {
        $path = 'tickets/' . $event->token . '.png';

        if (!Storage::disk('public')->exists($path)) {
            return;
        }

        Mail::to($event->person->email)->send(
            new AttendeeTicketMail($event->edition, $event->person, $event->token)
        );
    }
}
