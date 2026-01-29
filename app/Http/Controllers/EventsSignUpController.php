<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\AttendeSignUpEvent;
use App\Events\CancelSignUpEvent;
use App\Models\Event;
use App\Models\Attendee;

class EventsSignUpController extends Controller
{

    public function store($eventId)
    {
        $event = Event::find($eventId);
        $attendee = Attendee::find(auth()->id());
        //Se compilan todos los datos que se guardarán en el código QR.
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

        if($event->is_active)
            {
                if($event->capacity <=0)
                    {
                        return back()->withError('El evento no admite más inscripciones. Lamentamos las molestias.')->withInput();
                    }
                elseif(!$event->assistants->contains($attendee))
                    {
                        try{
                           return DB::transaction (function () use ($event, $attendee, $jsonPayLoad) {
                                $event = Event::where('id', $event->id)->lockForUpdate()->first();

                                if($event->capacity <=0)
                                    {
                                        throw new \Exception('El evento no admite más inscripciones. Lamentamos las molestias.');
                                    }

                                $event->assistants()->attach($attendee);

                                AttendeSignUpEvent::dispatch($event, $attendee, $jsonPayLoad);

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

    public function destroy($eventId)
    {
        $event = Event::find($eventId);
        $attendee = Attendee::find(auth()->id());

        try{
            return DB::transaction (function () use ($event, $attendee) {
                Event::where('id', $event->id)->lockForUpdate()->first();
                $event->assistants()->detach($attendee);
                CancelSignUpEvent::dispatch($event);
                return redirect('/attendee/dashboard')->with('success', 'Inscripción cancelada correctamente.');
            });    
        }
        catch (\Exception $e){
            return back()->withError('No ha sido posible cancelar la inscripción al evento.' . $e->getMessage())->withInput();
        }
    }
}
