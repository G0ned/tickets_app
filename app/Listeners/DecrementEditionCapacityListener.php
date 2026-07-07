<?php

namespace App\Listeners;

use App\Events\AttendeeEditionSignUpEvent;

class DecrementEditionCapacityListener
{
    public function handle(AttendeeEditionSignUpEvent $event): void
    {
        $event->edition->decrement('capacity');
    }
}
