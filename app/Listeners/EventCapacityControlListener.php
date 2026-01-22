<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Event;
use App\Events\AttendeSignUpEvent;
use App\Models\Event as EventModel;

class EventCapacityControlListener
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
      $event->event->decrement('capacity');
      $event->event->increment('number_of_attendees');
    }
}
