<?php

namespace App\Listeners;

use App\Events\AttendeeEditionCancelAssistance;

class IncrementEditionCapacityListener
{
    public function handle(AttendeeEditionCancelAssistance $event): void
    {
        $event->edition->increment('capacity');
    }
}
