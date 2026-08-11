<?php

namespace App\Http\Controllers;

use App\Models\InvitationList;
use App\Models\VerificationCode;
use App\Rules\ValidateId;
use App\Services\AttendeeRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvitationRegistrationController extends Controller
{
    public function __construct(private AttendeeRegistrationService $registrar)
    {
    }

    public function create(string $token)
    {
        $invitation = $this->resolveInvitation($token);

        if ($invitation === null) {
            return view('invitation.unavailable');
        }

        return view('invitation.register', [
            'edition' => $invitation->list->edition,
            'event'   => $invitation->list->edition->event,
            'token'   => $token,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $invitation = $this->resolveInvitation($token);

        if ($invitation === null) {
            return view('invitation.unavailable');
        }

        $validated = $request->validate([
            'identification'    => ['required', 'string', 'max:9', new ValidateId($request->input('id_type'))],
            'firstname'         => ['required', 'string', 'max:255'],
            'surname'           => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email'],
            'phone'             => ['required', 'string', 'max:20'],
            'zip_code'          => ['required', 'digits:5'],
            'img_rights_ads'    => ['required', 'boolean'],
            'img_rights_web'    => ['required', 'boolean'],
            'img_rights_rss'    => ['required', 'boolean'],
            'privacy_policy'    => ['required', 'in:1'],
            'verification_code' => ['required', 'string'],
        ]);

        $result = DB::transaction(function () use ($validated, $invitation) {
            $code = VerificationCode::where('code', $validated['verification_code'])
                ->lockForUpdate()
                ->first();

            if ($code === null) {
                return ['error' => 'El código de verificación no existe.'];
            }

            if ((int) $code->edition_id !== (int) $invitation->list->edition_id) {
                return ['error' => 'El código de verificación no corresponde a esta edición.'];
            }

            if ($code->isUsed()) {
                return ['error' => 'Este código de verificación ya ha sido utilizado.'];
            }

            $registration = $this->registrar->register($invitation->list->edition, $validated);

            if (isset($registration['error'])) {
                return $registration;
            }

            $code->update(['used_at' => now()]);

            DB::table('attendee_edition')
                ->where('edition_id', $invitation->list->edition_id)
                ->where('attendee_id', $registration['attendee']->id)
                ->update(['verification_code_id' => $code->id]);

            return $registration;
        });

        if (isset($result['error'])) {
            return back()->withErrors(['error' => $result['error']]);
        }

        DB::table('invitation_list_person')
            ->where('invitation_list_id', $invitation->invitationListId)
            ->where('person_id', $invitation->personId)
            ->increment('registrations_used');

        return redirect()->route('form-success');
    }

    /**
     * Looks up the invitation by its token via the query builder (not the Eloquent
     * Pivot model): a Pivot instance only knows its composite key columns when it's
     * instantiated through its parent BelongsToMany relation, so a standalone lookup
     * by token would break any later save()/increment() call.
     *
     * Returns null when the token is real but has run out of registrations — callers
     * render a friendly "unavailable" page for that case instead of an HTTP error page.
     * A token that doesn't exist at all (or belongs to a list that was never actually
     * sent) is a genuinely broken link, so that still aborts with 404.
     */
    private function resolveInvitation(string $token): ?object
    {
        $row = DB::table('invitation_list_person')->where('token', $token)->first();

        abort_if($row === null, 404);

        $list = InvitationList::with('edition.event')->findOrFail($row->invitation_list_id);

        abort_if($list->sent_at === null, 404);

        $remaining = $row->allowed_registrations - $row->registrations_used;

        if ($remaining <= 0) {
            return null;
        }

        return (object) [
            'invitationListId' => $row->invitation_list_id,
            'personId'         => $row->person_id,
            'list'             => $list,
        ];
    }
}
