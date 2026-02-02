<?php

namespace App\Listeners;

use App\Mail\EventTicketMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Event;
use App\Events\AttendeSignUpEvent;

class SendTicketkMail
{
    use InteractsWithQueue;
    public $tries = 3;
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
    public function handle(AttendeSignUpEvent $eventData): void
    {
        $event = $eventData->event;
        $attendee = $eventData->attendee;
        $user = $attendee->user;
        $qrPayload = [
            'ticket_id' => uniqid('ticket_'),
            'event_id' => $event->id,
            'user_id' => $user->identification,
            'user_name' => $user->firstname,
            'user_surname' => $user->surname,
            'event_name' => $event->name,
            'event_date' => $event->date,
            'event_location' => $event->location
        ];
        $fileName = $event->id . '_' . $event->name . '_' . $user->surname . '_' . $user->name . '.svg';
        try{
            $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate(json_encode($qrPayload));

            Storage::disk('public')->put('tickets_qr/' . $fileName, $qrCode);

            Mail::to($user->email)->send(new EventTicketMail($event, $user, 'tickets_qr/' . $fileName));
        }
        catch (\Exception $e){
            //Log error
            \Log::error('Error generating QR code for ticket: ' . $e->getMessage());
            return;
        }
        
    }
}