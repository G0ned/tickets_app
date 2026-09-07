<?php

namespace App\Http\Controllers;

use App\Events\AttendeeEditionCancelAssistance;
use App\Models\Edition;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class AttendeeCancellationController extends Controller
{
    /**
     * Shows a confirmation page for the attendee's own cancellation link
     * (embedded in their ticket email). Public, no auth: the attendee has
     * no account, so the ticket token is what authenticates the request -
     * same pattern as InvitationRegistrationController's token-based routes.
     */
    public function create(string $token)
    {
        $row = $this->resolveAttendance($token);

        if ($row === null) {
            return view('attendee.cancel-unavailable', [
                'reason' => 'Este enlace de cancelación no es válido o ya se ha utilizado.',
            ]);
        }

        if ($row->attendance) {
            return view('attendee.cancel-unavailable', [
                'reason' => 'Ya se ha registrado tu asistencia a este evento, por lo que no es posible cancelar la inscripción.',
            ]);
        }

        $edition = Edition::with('event')->findOrFail($row->edition_id);

        if ($edition->hasEnded()) {
            return view('attendee.cancel-unavailable', [
                'reason' => 'El evento ya se ha celebrado, por lo que no es posible cancelar la inscripción.',
            ]);
        }

        $attendee = Person::findOrFail($row->attendee_id);

        return view('attendee.cancel-confirm', [
            'edition'  => $edition,
            'attendee' => $attendee,
            'token'    => $token,
        ]);
    }

    public function store(string $token)
    {
        $row = $this->resolveAttendance($token);

        if ($row === null) {
            return view('attendee.cancel-unavailable', [
                'reason' => 'Este enlace de cancelación no es válido o ya se ha utilizado.',
            ]);
        }

        if ($row->attendance) {
            return view('attendee.cancel-unavailable', [
                'reason' => 'Ya se ha registrado tu asistencia a este evento, por lo que no es posible cancelar la inscripción.',
            ]);
        }

        $edition = Edition::with('event')->findOrFail($row->edition_id);

        if ($edition->hasEnded()) {
            return view('attendee.cancel-unavailable', [
                'reason' => 'El evento ya se ha celebrado, por lo que no es posible cancelar la inscripción.',
            ]);
        }

        $attendee = Person::findOrFail($row->attendee_id);

        DB::table('attendee_edition')
            ->where('edition_id', $edition->id)
            ->where('attendee_id', $attendee->id)
            ->update(['cancelled_at' => now()]);
        AttendeeEditionCancelAssistance::dispatch($edition, $attendee, $row->verification_code_id, $row->token);

        return view('attendee.cancel-success', ['edition' => $edition]);
    }

    private function resolveAttendance(string $token): ?object
    {
        // whereNull('cancelled_at') keeps a second visit to an already-used
        // cancellation link behaving exactly as before: the row still exists
        // now (it's flagged, not detached), but must resolve as "not found"
        // here just like it did when the row was actually gone.
        return DB::table('attendee_edition')->where('token', $token)->whereNull('cancelled_at')->first();
    }
}
