<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Person;
use App\Models\Edition;
use App\Rules\ValidateId;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Events\AttendeeEditionSignUpEvent as signup_event;
use App\Events\AttendeeEditionCancelAssistance as cancel_assistance;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

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

        return DB::transaction(function () use ($validated) {
            $edition = Edition::where('id', $validated['editions'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($edition->capacity <= 0) {
                return back()->withErrors(['error' => 'La edición no admite a más asistentes. Pruebe a registrarse en otra edición.']);
            }
            $attendee = Person::firstOrCreate(
                ['passport' => $validated['identification']],
                [
                    'name'    => $validated['firstname'],
                    'surname' => $validated['surname'],
                    'email'   => $validated['email'],
                    'phone'   => $validated['phone'],
                ]
            );

            if ($edition->attendees()->where('attendee_id', $attendee->id)->exists()) {
                return back()->withErrors(['error' => 'Ya estás registrado en esta edición.']);
            }

            $ticket_token = (string) Str::uuid();

            $edition->attendees()->attach($attendee->id, [
                'auth_for_ad'       => $validated['img_rights_ads'],
                'auth_for_comms'    => $validated['img_rights_web'],
                'auth_image_rights' => $validated['img_rights_rss'],
                'privacy_policy'    => $validated['privacy_policy'],
                'token' => $ticket_token
            ]);

            $qr = QrCode::format('png')->size(300)->generate($ticket_token);
            Storage::put('tickets/' . $ticket_token . '.png', $qr);

            signup_event::dispatch($edition, $attendee);

            return redirect()->route('form-success');
        });
    }
    public function cancel_attendee(Request $request, Edition $edition, Person $attendee)
    {
        $edition->attendees()->detach($attendee->id);
        cancel_assistance::dispatch($edition, $attendee);

        return redirect()->route('edition-attendees', ['edition' => $edition->id]);
    }
}
