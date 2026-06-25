<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AttendeeEditionSignUpEvent;
class EditionCapacityControlListener
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
    public function handle(AttendeeEditionSignUpEvent $event): void
    {
        $event->edition->decrement('capacity');
    }
}
