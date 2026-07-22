<?php

namespace App\Observers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    public function deleting(User $user): void
    {
        $events = DB::table('event_organizer')->where('user_id', $user->id)->get();
        if($events->isNotEmpty())
            {
                foreach($events as $event){
                    if(DB::table('event_organizer')->where('event_id', $event->event_id)->where('user_id', Auth::id())->exists()){
                        $updates = [];
                        if ($event->is_organizer) $updates['is_organizer'] = true;
                        if ($event->is_doorman)   $updates['is_doorman']   = true;

                        if (!empty($updates)) {
                            DB::table('event_organizer')
                                ->where('event_id', $event->event_id)
                                ->where('user_id', Auth::id())
                                ->update($updates);
                        }

                        DB::table('event_organizer')->where('event_id', $event->event_id)->where('user_id', $user->id)->delete();
                    }
                    else{
                        DB::table('event_organizer')->where('event_id', $event->event_id)->where('user_id', $user->id)
                        ->update(['user_id' => Auth::id()]);
                    }
                }
            }
    }
}
