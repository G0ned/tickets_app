<?php

namespace App\Services;

use App\Events\AttendeeEditionSignUpEvent;
use App\Models\Edition;
use App\Models\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendeeRegistrationService
{
    /**
     * Registers a person as an attendee of the given edition: checks capacity,
     * deduplicates by passport, generates the QR ticket and dispatches the
     * sign-up event (ticket email, capacity decrement).
     *
     * @param array{identification: string, firstname: string, surname: string, email: string, phone: string, img_rights_ads: mixed, img_rights_web: mixed, img_rights_rss: mixed, privacy_policy: mixed} $validated
     * @return array{error: string}|array{attendee: Person}
     */
    public function register(Edition $edition, array $validated): array
    {
        return DB::transaction(function () use ($edition, $validated) {
            $edition = Edition::where('id', $edition->id)->lockForUpdate()->firstOrFail();

            if ($edition->capacity <= 0) {
                return ['error' => 'La edición no admite a más asistentes. Pruebe a registrarse en otra edición.'];
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
                return ['error' => 'Ya estás registrado en esta edición.'];
            }

            $ticketToken = (string) Str::uuid();

            $edition->attendees()->attach($attendee->id, [
                'auth_for_ad'       => $validated['img_rights_ads'],
                'auth_for_comms'    => $validated['img_rights_web'],
                'auth_image_rights' => $validated['img_rights_rss'],
                'privacy_policy'    => $validated['privacy_policy'],
                'token'             => $ticketToken,
            ]);

            $qr = QrCode::format('png')->size(300)->generate($ticketToken);
            Storage::disk('public')->put('tickets/' . $ticketToken . '.png', $qr);

            AttendeeEditionSignUpEvent::dispatch($edition, $attendee, $ticketToken);

            return ['attendee' => $attendee];
        });
    }
}
