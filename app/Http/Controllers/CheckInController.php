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
        if ($attendee->events()->where('id', $validated['event_id'])->first()->pivot->has_attended) { //this is how to access to the attribute values of the pivot table
            return response()->json([
                'status' => 'warning', 
                'message' => ''
            ], 200);
        }
        else{
            try
            {
                $attendee->events()->updateExistingPivot($event, [
                'has_attended' => true,
                'checked_in_at' => now(),
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Welcome, ' . $attendee->user->name . '!'
                ]);
            }
            catch(\Exception $e)
            {
                return response()->json([
                    'status' => 'error',
                    'message' => " $e"
                ]);
            }
        }
    }
}