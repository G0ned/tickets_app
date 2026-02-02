<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Event;
use App\Models\Attendee;

class AttendeSignUpEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public \App\Models\Event $event, public \App\Models\Attendee $attendee){}
    
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('attendee-signup-channel'),
        ];
    }
}
