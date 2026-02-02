<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CancelSignUpEvent;

class CancelEventInscriptionListener
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
    public function handle(CancelSignUpEvent $event): void
    {
        $event->event->increment('capacity');
        $event->event->decrement('number_of_attendees');
    }
}
