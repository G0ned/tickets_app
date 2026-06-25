<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Edition;
use App\Models\Person;

class AttendeeEditionSignUpEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public \App\Models\Edition $edition, public \App\Models\Person $person )
    {
        //
    }
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('person-signup-channel'),
        ];
    }
}
