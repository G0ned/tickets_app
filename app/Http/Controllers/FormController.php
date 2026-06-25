<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Person;
use App\Models\Edition;
use App\Rules\ValidateId;
use Illuminate\Support\Facades\DB;
class FormController extends Controller
{
    public function create(Event $event)
    {
        $event->load('editions');

        return view('form.create')->with('event', $event);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'editions'       => ['required', 'exists:editions,id'],
            'identification' => ['required', 'string', 'max:9', new ValidateId($request->input('id_type'))],
            'firstname'      => ['required', 'string', 'max:255'],
            'surname'        => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email'],
            'phone'          => ['required', 'string', 'max:20'],
            'zip_code'       => ['required', 'digits:5'],
            'img_rights_ads' => ['required', 'boolean'],
            'img_rights_web' => ['required', 'boolean'],
            'img_rights_rss' => ['required', 'boolean'],
            'privacy_policy' => ['required', 'in:1'],
        ]);

        return DB::transaction(function() use ($validated)
        {
            $edition = Edition::find($validated['editions']);
            if($edition->capacity <= 0)
                {
                    return back()->withErrors(['error', 'La edición no admite a más asistentes. Pruebe a registrarse en otra edición']);
                }

            elseif(Edition::find($validated['editions'])->attendees->contains('attendee_id', $validated['identification']))
                {
                    return back()->withErrors(['error' ,'Usuario ya registrado en esta edición...']);
                }
            elseif(!Person::where('passport', $validated['identification'])->exists())
                {
                    $attendee = Person::create([
                        'name' => $validated['firstname'],
                        'surname' => $validated['surname'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'passport' => $validated['identification'],
                    ]);
                }
            else
                {

                    $attendee = Person::where('passport', $validated['identification'])->first();
                }
            
            $edition->lockForUpdate();
            $edition->attendees()->attach($attendee->id, [
                'auth_for_ad' => $validated['img_rights_ads'],
                'auth_for_comms' => $validated['img_rights_web'],
                'auth_image_rights' => $validated['img_rights_rss'],
                'privacy_policy' => $validated['privacy_policy']
            ]);
            return redirect()->route('form-success');
        });
    }
}
