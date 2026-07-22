<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Person;
use App\Models\Edition;
use App\Rules\ValidateId;
use App\Services\AttendeeRegistrationService;
use App\Events\AttendeeEditionCancelAssistance as cancel_assistance;
use Illuminate\Support\Facades\Storage;

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
        ], [
            'editions.required'       => 'Debe seleccionar una edición.',
            'editions.exists'         => 'La edición seleccionada no es válida.',
            'identification.required' => 'El número de identificación es obligatorio.',
            'identification.string'   => 'El número de identificación no es válido.',
            'identification.max'      => 'El número de identificación no puede tener más de :max caracteres.',
            'firstname.required'      => 'El nombre es obligatorio.',
            'firstname.string'        => 'El nombre no es válido.',
            'firstname.max'           => 'El nombre no puede tener más de :max caracteres.',
            'surname.required'        => 'Los apellidos son obligatorios.',
            'surname.string'          => 'Los apellidos no son válidos.',
            'surname.max'             => 'Los apellidos no pueden tener más de :max caracteres.',
            'email.required'          => 'El e-mail es obligatorio.',
            'email.email'             => 'El e-mail no es válido.',
            'phone.required'          => 'El teléfono es obligatorio.',
            'phone.string'            => 'El teléfono no es válido.',
            'phone.max'               => 'El teléfono no puede tener más de :max caracteres.',
            'zip_code.required'       => 'El código postal es obligatorio.',
            'zip_code.digits'         => 'El código postal debe tener :digits dígitos.',
            'img_rights_ads.required' => 'Debe indicar si autoriza el uso publicitario.',
            'img_rights_ads.boolean'  => 'La opción de autorización publicitaria no es válida.',
            'img_rights_web.required' => 'Debe indicar si autoriza las comunicaciones.',
            'img_rights_web.boolean'  => 'La opción de autorización de comunicaciones no es válida.',
            'img_rights_rss.required' => 'Debe indicar si autoriza el uso en redes sociales.',
            'img_rights_rss.boolean'  => 'La opción de autorización de redes sociales no es válida.',
            'privacy_policy.required' => 'Debe aceptar la Política de Privacidad.',
            'privacy_policy.in'       => 'Debe aceptar la Política de Privacidad para continuar.',
        ]);

        $edition = Edition::findOrFail($validated['editions']);

        $result = $this->registrar->register($edition, $validated);

        if (isset($result['error'])) {
            return back()->withErrors(['error' => $result['error']]);
        }

        return redirect()->route('form-success');
    }

    public function downloadTicket(Edition $edition, Person $attendee)
    {
        $token = $edition->attendees()->find($attendee->id)->pivot->token;
        $path = 'tickets/' . $token . '.png';
        abort_unless(Storage::disk('public')->exists($path), 404);
        return Storage::disk('public')->download($path, 'ticket-' . $attendee->surname . '-' . $attendee->name . '-' . $edition->id . '.png');
    }


    public function cancel_attendee(Request $request, Edition $edition, Person $attendee)
    {
        $edition->attendees()->detach($attendee->id);
        cancel_assistance::dispatch($edition, $attendee);

        return redirect()->route('edition-attendees', ['edition' => $edition->id]);
    }
}
