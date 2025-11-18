<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Attendee;

class EventsSignUpController extends Controller
{

    public function store($eventId)
    {
        $event = Event::find($eventId);
        $attendee = Attendee::find(auth()->id());

        if($event->is_active)
            {
                if(!$event->assistants->contains($attendee))
                    {
                        try{
                            $event->assistants()->attach($attendee);
                            return redirect('/dashboard')->with('success', 'Inscripción realizada correctamente.');
                        }
                        catch (\Exception $e){
                            return back()->withError('No ha sido posible inscribirse al evento.' . $e->getMessage())->withInput();
                        }
                        
                    }
            }
    }
}
