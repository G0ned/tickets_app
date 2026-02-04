<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function create()
    {
        return view("admin.scanner");
    }
    public function update()
    {
        $validated = request()->validate([
            'user_id' => 'required|string',
            'event_id' => 'required|integer',
        ]);

        $attendee = Attendee::find($validated['user_id']);
        $event = Event::find($validated['event_id']);
        
        if (!$attendee) {
            return response()->json(['status' => 'error', 'message' => 'Ticket not found!'], 404);
        }
        
        $not_accepted_rights = [];
        if ($attendee->img_rights_ads == 0) {
            $not_accepted_rights[] = 'Derechos de imagen para publicidad';
        }
        if ($attendee->img_rights_web == 0) {
            $not_accepted_rights[] = 'Derechos de imagen para la web';
        }
        if ($attendee->img_rights_rss == 0) {
            $not_accepted_rights[] = 'Derechos de imagen para RSS';
        }
        
        if ($attendee->events()->where('id', $validated['event_id'])->first()->pivot->has_attended) {
            $message = 'Esta entrada ya ha ha sido escaneada.';
            if (!empty($not_accepted_rights)) {
                $message .= ' Derechos revocados: ' . implode(', ', $not_accepted_rights);
            }
            return response()->json([
                'status' => 'warning', 
                'message' => $message,
                'not_accepted_rights' => $not_accepted_rights
            ], 200);
        }
        else {
            try
            {
                $attendee->events()->updateExistingPivot($event, [
                    'has_attended' => true,
                    'checked_in_at' => now(),
                ]);
                
                $message = 'Bienvenido ' . $attendee->user->firstname . '!';
                
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'not_accepted_rights' => $not_accepted_rights
                ]);
            }
            catch(\Exception $e)
            {
                return response()->json([
                    'status' => 'error',
                    'message' => "Ha ocurrido un error: " . $e->getMessage()
                ]);
            }
        }
    }
}