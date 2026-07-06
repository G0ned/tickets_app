<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Edition;
use App\Models\Person;

class AttendeeEditionCancelAssistance
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Edition $edition, public Person $person)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('person-cancel-assistance-channel'),
        ];
    }
}
