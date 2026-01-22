<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use App\Events\AttendeSignUpEvent;
use App\Models\Event;
use App\Models\Attendee;

class EventsSignUpController extends Controller
{

    public function store($eventId)
    {
        $event = Event::find($eventId);
        $attendee = Attendee::find(auth()->id());
        //Compile all the data that will be saved on the QR code Ticket
        $ticketData = 
        [
            'id' => $event->id,
            'at_id' => $attendee->user->identification,
            'at_name' => $attendee->user->firstname,
            'at_surname' => $attendee->user->surname,
            'event_name' => $event->name,
            'event_date' => $event->date,
            'event_location' => $event->location
        ];
      
        $jsonPayLoad = json_encode($ticketData);
        $qrContent = (string) $jsonPayLoad;

        if($event->is_active)
            {
                if(!$event->assistants->contains($attendee))
                    {
                        try{
                           return DB::transaction (function () use ($event, $attendee, $jsonPayLoad) {
                                $event->assistants()->attach($attendee);
                                AttendeSignUpEvent::dispatch($event);
                                $fileName = "/tickets/ticket_event_{$event->id}_attendee_ {$attendee->user->firstname}_{$attendee->user->surname}.svg";
                                $qrImage = QrCode::format('svg')
                                                ->size(300)
                                                ->generate($jsonPayLoad);
                                Storage::disk('public')->put($fileName, $qrImage);
                                return redirect('/attendee/dashboard')->with('success', 'Inscripción realizada correctamente.');
                            });    
                        }
                        catch (\Exception $e){
                            return back()->withError('No ha sido posible inscribirse al evento.' . $e->getMessage())->withInput();
                        }
                        
                    }
                else
                    {
                        return back()->withError('Ya estás inscrito en este evento.')->withInput();
                    }
            }
    }
}
