<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Edition;
use App\Models\User;
use App\Models\VerificationCode;
use App\Models\AttendeeEditionHistory;
use App\Exports\AttendeesExport;
use App\Mail\EditionRestoredMail;
use App\Services\EditionCancellationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
class EditionController extends Controller
{
    public function __construct(private EditionCancellationService $cancellation)
    {
    }

    public function create(Event $event)
    {
        if (!(Auth::user()->is_admin || $event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $event->id)->with('error', 'No tienes permisos para esta acción');
        }

        return view('editions.create')->with('event', $event);
    }

    public function store(Event $event)
    {
        if (!(Auth::user()->is_admin || $event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $event->id)->with('error', 'No tienes permisos para esta acción');
        }

        $validated = request()->validate([
            'location'                                  => ['required', 'string'],
            'duration'                                   => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'capacity'                                   => ['required', 'numeric', 'min:0'],
            'occurrences'                                => ['required', 'array', 'min:1'],
            'occurrences.*.date'                         => ['required', 'date'],
            'occurrences.*.time'                         => ['required', 'date_format:H:i'],
            'occurrences.*.registration_deadline_date'   => ['nullable', 'date'],
            'occurrences.*.registration_deadline_time'   => ['nullable', 'date_format:H:i'],
        ]);

        $datetimes = [];
        $occurrences = [];
        foreach ($validated['occurrences'] as $index => $occurrence) {
            $datetime = $occurrence['date'] . ' ' . $occurrence['time'];

            if (in_array($datetime, $datetimes, true)) {
                return back()
                    ->withErrors(["occurrences.$index.date" => 'Esta fecha y hora está repetida en el formulario.'])
                    ->withInput();
            }

            $hasDeadlineDate = !empty($occurrence['registration_deadline_date']);
            $hasDeadlineTime = !empty($occurrence['registration_deadline_time']);

            if ($hasDeadlineDate !== $hasDeadlineTime) {
                return back()
                    ->withErrors(["occurrences.$index.registration_deadline_date" => 'Si defines un plazo límite de inscripción, indica tanto la fecha como la hora.'])
                    ->withInput();
            }

            $datetimes[] = $datetime;
            $occurrences[] = [
                'date'                  => $datetime,
                'registration_deadline' => $hasDeadlineDate
                    ? $occurrence['registration_deadline_date'] . ' ' . $occurrence['registration_deadline_time']
                    : null,
            ];
        }

        $conflicting = Edition::where('location', $validated['location'])
            ->whereIn('date', $datetimes)
            ->pluck('date');

        if ($conflicting->isNotEmpty()) {
            $formatted = $conflicting->map(fn ($d) => Carbon::parse($d)->format('d/m/Y H:i'))->join(', ');

            return back()
                ->withErrors(['occurrences' => "Ya existe una edición en {$validated['location']} para: {$formatted}."])
                ->withInput();
        }

        try {
            DB::transaction(function () use ($event, $validated, $occurrences) {
                foreach ($occurrences as $occurrence) {
                    Edition::create([
                        'event_id'               => $event->id,
                        'location'               => $validated['location'],
                        'date'                   => $occurrence['date'],
                        'duration'               => $validated['duration'],
                        'capacity'               => $validated['capacity'],
                        'status'                 => false,
                        'registration_deadline'  => $occurrence['registration_deadline'],
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors('Error:' . $e->getMessage())->withInput();
        }

        $count = count($datetimes);

        return redirect(route('events-index'))->with('success', $count === 1
            ? 'Edición creada correctamente.'
            : "{$count} ediciones creadas correctamente.");
    }

    public function edit(Edition $edition)
    {
        if (!(Auth::user()->is_admin || $edition->event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $edition->event_id)->with('error', 'No tienes permisos para esta acción');
        }

        $edition->load(['managers', 'reminders']);
        $assignableUsers = User::whereNotIn('id', $edition->managers->pluck('id'))->get();
        $historyCount = AttendeeEditionHistory::where('edition_id', $edition->id)->count();

        return view('editions.edit', compact('edition', 'assignableUsers', 'historyCount'));
    }

    public function update(Edition $edition)
    {
        if (!(Auth::user()->is_admin || $edition->event->organizers->contains('id', Auth::id()))) {
            return redirect()->route('events-show', $edition->event_id)->with('error', 'No tienes permisos para esta acción');
        }

        $validated = request()->validate([
            'location' => ['required', 'string'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'capacity' => ['required', 'numeric', 'min:0'],
            'registration_deadline_date' => ['nullable', 'date', 'required_with:registration_deadline_time'],
            'registration_deadline_time' => ['nullable', 'date_format:H:i', 'required_with:registration_deadline_date'],
        ]);

        $registrationDeadline = !empty($validated['registration_deadline_date'])
            ? $validated['registration_deadline_date'] . ' ' . $validated['registration_deadline_time']
            : null;

        try
        {
            $edition->update([
                'location' => $validated['location'],
                'date' => $validated['date'] . " " . $validated['time'],
                'duration' => $validated['duration'],
                'capacity' => $validated['capacity'],
                'registration_deadline' => $registrationDeadline,
            ]);
            return redirect(route('events-index'));
        }
        catch(\Exception $e)
        {
            return back()->withErrors('Error: '. $e->getMessage()); 
        }
        
    }

    public function destroy(Edition $edition)
    {
        if ($edition->hasEnded()) {
            $edition->delete();
            return redirect(route('events-index'));
        }

        $this->cancellation->cancel($edition);

        return redirect()->route('events-index')
            ->with('success', 'La edición se ha cancelado y se ha notificado a los asistentes inscritos.');
    }
    
    public function cancel(Edition $edition)
    {
        if ($edition->hasEnded()) {
            return redirect()->route('editions-edit', $edition->id)
                ->with('error', 'No es posible cancelar una edición que ya se ha celebrado.');
        }

        $this->cancellation->cancel($edition);

        return redirect()->route('events-index')
            ->with('success', 'La edición se ha cancelado y se ha notificado a los asistentes inscritos.');
    }

    public function restore(Edition $edition)
    {
        $edition->restore();

        $edition->load(['event', 'managers']);
        foreach ($edition->managers as $manager) {
            Mail::to($manager->email)->queue(new EditionRestoredMail($edition, $manager));
        }

        DB::transaction(function () use ($edition) {
            $activeRegistrations = DB::table('attendee_edition')
                ->where('edition_id', $edition->id)
                ->whereNull('cancelled_at')
                ->get();

            if ($activeRegistrations->isEmpty()) {
                return;
            }

            $archivedAt = now();
            DB::table('attendee_edition_history')->insert(
                $activeRegistrations->map(fn ($registration) => [
                    'edition_id'            => $edition->id,
                    'attendee_id'           => $registration->attendee_id,
                    'token'                 => $registration->token,
                    'auth_for_ad'           => $registration->auth_for_ad,
                    'auth_for_comms'        => $registration->auth_for_comms,
                    'auth_image_rights'     => $registration->auth_image_rights,
                    'privacy_policy'        => $registration->privacy_policy,
                    'attendance'            => $registration->attendance,
                    'checked_in_at'         => $registration->checked_in_at,
                    'verification_code_id'  => $registration->verification_code_id,
                    'is_guest'              => $registration->is_guest,
                    'registered_at'         => $registration->created_at,
                    'archived_at'           => $archivedAt,
                ])->all()
            );

            DB::table('attendee_edition')
                ->where('edition_id', $edition->id)
                ->whereNull('cancelled_at')
                ->delete();

            $edition->increment('capacity', $activeRegistrations->count());

            $verificationCodeIds = $activeRegistrations->pluck('verification_code_id')->filter();
            if ($verificationCodeIds->isNotEmpty()) {
                VerificationCode::whereIn('id', $verificationCodeIds)->update(['used_at' => null]);
            }
        });

        return redirect()->route('events-show', $edition->event_id)
            ->with('success', 'La edición se ha reactivado correctamente, sin las inscripciones previas.');
    }

    public function assignManager(Request $request, Edition $edition)
    {
        $validated = $request->validate([
            'user_id'              => ['required', 'exists:users,id'],
            'is_supervisor'        => ['boolean'],
            'is_doorman'           => ['boolean'],
            'invitations_capacity' => ['nullable', 'integer', 'min:0'],
        ]);

        $edition->managers()->attach($validated['user_id'], [
            'is_supervisor'        => $validated['is_supervisor'] ?? false,
            'is_doorman'           => $validated['is_doorman'] ?? false,
            'invitations_capacity' => $validated['invitations_capacity'] ?? null,
        ]);

        return redirect()->route('editions-edit', $edition->id)
            ->with('success', 'Gestor asignado correctamente.');
    }

    public function managerEditions(User $user)
    {
        $user_editions = $user->managed_events()->with('event')->get();
        return view('editions.manager-editions')->with('editions', $user_editions);
    }

    public function attendees(Edition $edition)
    {
        $edition->load(['event', 'attendees']);
        $inviterNames = $edition->guestInviterNames();

        return view('editions.attendees', compact('edition', 'inviterNames'));
    }

    public function cancellationHistory(Edition $edition)
    {
        $edition->load('event');

        $history = AttendeeEditionHistory::where('edition_id', $edition->id)
            ->with(['attendee', 'verificationCode.person'])
            ->orderByDesc('archived_at')
            ->orderBy('registered_at')
            ->get();

        return view('editions.history', compact('edition', 'history'));
    }

    public function exportAttendees(Edition $edition, Request $request)
    {
        $edition->load(['event', 'attendees']);

        if ($request->query('format') === 'xlsx') {
            return Excel::download(new AttendeesExport($edition), "{$edition->event->name}-asistentes-edicion-{$edition->id}.xlsx");
        }

        $inviterNames = $edition->guestInviterNames();

        return response()->streamDownload(
            function() use ($edition, $inviterNames)
            {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Evento', 'ID edicion', 'Nombre', 'Apellidos', 'Identificación', 'e-mail', 'Teléfono',
                'Derechos para publicidad', 'Derechos para comunicaciones', 'Derechos de imagen', 'Politica de privacidad', 'Asistió', 'Hora de entrada', 'Invitado', 'Invitado por']);
                    foreach($edition->attendees as $attendee){
                        // Not a guest (public sign-up, or the client used their own code): always a hyphen.
                        $invitedBy = $attendee->pivot->is_guest
                            ? ($inviterNames[$attendee->pivot->verification_code_id] ?? '-')
                            : '-';
                        fputcsv($handle, [$edition->event->name, $edition->id, $attendee->name, $attendee->surname, $attendee->passport,
                        $attendee->email, $attendee->phone, $attendee->pivot->auth_for_ad ? 'Si' : 'No', $attendee->pivot->auth_for_comms ? 'Si' : 'No',
                        $attendee->pivot->auth_image_rights ? 'Si' : 'No', $attendee->pivot->privacy_policy ? 'Si' : 'No', $attendee->pivot->attendance ? 'Si' : 'No', $attendee->pivot->checked_in_at ? \Carbon\Carbon::parse($attendee->pivot->checked_in_at)->format('d/m/Y H:i'):'-', $attendee->pivot->is_guest ? 'Si' : 'No', $invitedBy]);
                    }
                fclose($handle);
            }, "asistentes-{$edition->event->name}-edicion-{$edition->id}.csv", ['Content-Type' => 'text/csv']);
    }
}
