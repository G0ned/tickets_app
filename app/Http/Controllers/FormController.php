<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Person;
use App\Models\Edition;
use App\Services\AttendeeRegistrationService;
use App\Events\AttendeeEditionCancelAssistance as cancel_assistance;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
{
    public function __construct(private AttendeeRegistrationService $registrar)
    {
    }

    public function create(Event $event)
    {
        $event->load('editions');

        return view('form.create')->with('event', $event);
    }

    public function store(Request $request)
    {
        $editionValidated = $request->validate([
            'editions' => ['required', 'exists:editions,id'],
        ], [
            'editions.required' => 'Debe seleccionar una edición.',
            'editions.exists'   => 'La edición seleccionada no es válida.',
        ]);

        $edition = Edition::findOrFail($editionValidated['editions']);

        if ($edition->registrationClosed()) {
            $existingPerson = $request->filled('identification')
                ? Person::where('passport', $request->input('identification'))->first()
                : Person::where('email', $request->input('email'))->first();

            return view('form.registration-closed', [
                'deadline' => $edition->registration_deadline,
                'manager'  => $existingPerson?->portfolio?->user,
            ]);
        }

        $identity = $this->registrar->resolveIdentity($request);

        if (isset($identity['error'])) {
            return back()->withErrors(['error' => $identity['error']])->withInput();
        }

        $consents = $request->validate([
            'img_rights_ads' => ['required', 'boolean'],
            'img_rights_web' => ['required', 'boolean'],
            'img_rights_rss' => ['required', 'boolean'],
            'privacy_policy' => ['required', 'in:1'],
        ], [
            'img_rights_ads.required' => 'Debe indicar si autoriza el uso publicitario.',
            'img_rights_ads.boolean'  => 'La opción de autorización publicitaria no es válida.',
            'img_rights_web.required' => 'Debe indicar si autoriza las comunicaciones.',
            'img_rights_web.boolean'  => 'La opción de autorización de comunicaciones no es válida.',
            'img_rights_rss.required' => 'Debe indicar si autoriza el uso en redes sociales.',
            'img_rights_rss.boolean'  => 'La opción de autorización de redes sociales no es válida.',
            'privacy_policy.required' => 'Debe aceptar la Política de Privacidad.',
            'privacy_policy.in'       => 'Debe aceptar la Política de Privacidad para continuar.',
        ]);

        if ($request->input('registration_mode') !== 'returning') {
            $request->validate(['zip_code' => ['required', 'digits:5']], [
                'zip_code.required' => 'El código postal es obligatorio.',
                'zip_code.digits'   => 'El código postal debe tener :digits dígitos.',
            ]);
        }

        $result = $this->registrar->register($edition, array_merge($identity, $consents));

        if (isset($result['error'])) {
            return back()->withErrors(['error' => $result['error']])->withInput();
        }

        return redirect()->route('form-success');
    }

    public function downloadTicket(Edition $edition, Person $attendee)
    {
        $token = $edition->attendees()->find($attendee->id)?->pivot->token;
        abort_unless($token !== null, 404);
        $path = 'tickets/' . $token . '.png';
        abort_unless(Storage::disk('public')->exists($path), 404);
        return Storage::disk('public')->download($path, 'ticket-' . $attendee->surname . '-' . $attendee->name . '-' . $edition->id . '.png');
    }


    public function cancel_attendee(Request $request, Edition $edition, Person $attendee)
    {
        $pivot = $edition->attendees()->find($attendee->id)?->pivot;

        if ($pivot === null) {
            return redirect()->route('edition-attendees', ['edition' => $edition->id])
                ->with('error', 'El asistente ya no está inscrito en esta edición.');
        }

        if ($pivot->attendance) {
            return redirect()->route('edition-attendees', ['edition' => $edition->id])
                ->with('error', 'No es posible cancelar la inscripción: la entrada ya ha sido escaneada.');
        }

        if ($edition->hasEnded()) {
            return redirect()->route('edition-attendees', ['edition' => $edition->id])
                ->with('error', 'No es posible cancelar la inscripción: el evento ya se ha celebrado.');
        }

        DB::table('attendee_edition')
            ->where('edition_id', $edition->id)
            ->where('attendee_id', $attendee->id)
            ->update(['cancelled_at' => now()]);
        cancel_assistance::dispatch($edition, $attendee, $pivot->verification_code_id, $pivot->token);

        return redirect()->route('edition-attendees', ['edition' => $edition->id]);
    }
}
