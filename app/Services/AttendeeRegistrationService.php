<?php

namespace App\Services;

use App\Events\AttendeeEditionSignUpEvent;
use App\Models\Edition;
use App\Models\Person;
use App\Rules\ValidateId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendeeRegistrationService
{
    public function resolveIdentity(Request $request): array
    {
        if ($request->input('registration_mode') === 'returning') {
            $validated = $request->validate([
                'email' => ['required', 'email'],
            ], [
                'email.required' => 'El e-mail es obligatorio.',
                'email.email'    => 'El e-mail no es válido.',
            ]);

            $person = Person::where('email', $validated['email'])->first();

            if ($person === null) {
                return ['error' => 'No encontramos ningún registro con ese e-mail. Si es la primera vez que te registras en un evento, selecciona esa opción arriba.'];
            }

            return [
                'identification' => $person->passport,
                'firstname'      => $person->name,
                'surname'        => $person->surname,
                'email'          => $person->email,
                'phone'          => $person->phone,
            ];
        }

        return $request->validate([
            'identification' => ['required', 'string', 'max:9', new ValidateId($request->input('id_type'))],
            'firstname'      => ['required', 'string', 'max:255'],
            'surname'        => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email'],
            'phone'          => ['required', 'string', 'max:20'],
        ], [
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
        ]);
    }

    public function register(Edition $edition, array $validated): array
    {
        return DB::transaction(function () use ($edition, $validated) {
            $edition = Edition::where('id', $edition->id)->lockForUpdate()->firstOrFail();

            if ($edition->capacity <= 0) {
                return ['error' => 'La edición no admite a más asistentes. Pruebe a registrarse en otra edición.'];
            }

            if (!Person::where('passport', $validated['identification'])->exists()) {
                $emailTaken = Person::where('email', $validated['email'])->exists();
                $phoneTaken = !$emailTaken && Person::where('phone', $validated['phone'])->exists();

                if ($emailTaken || $phoneTaken) {
                    $field = $emailTaken ? 'email' : 'número de teléfono';
                    return ['error' => "Ya existe otra persona registrada con ese {$field}. Revisa que lo has escrito bien, o contacta con el organizador si crees que es un error."];
                }
            }

            try {
                $attendee = Person::firstOrCreate(
                    ['passport' => $validated['identification']],
                    [
                        'name'    => $validated['firstname'],
                        'surname' => $validated['surname'],
                        'email'   => $validated['email'],
                        'phone'   => $validated['phone'],
                    ]
                );
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                return ['error' => 'Ya existe otra persona registrada con ese email o teléfono. Revisa que lo has escrito bien, o contacta con el organizador si crees que es un error.'];
            }

            if ($edition->attendees()->where('attendee_id', $attendee->id)->exists()) {
                return ['error' => 'Ya estás registrado en esta edición.'];
            }

            $ticketToken = (string) Str::uuid();

            $cancelledRow = DB::table('attendee_edition')
                ->where('edition_id', $edition->id)
                ->where('attendee_id', $attendee->id)
                ->whereNotNull('cancelled_at')
                ->exists();

            $pivotData = [
                'auth_for_ad'          => $validated['img_rights_ads'],
                'auth_for_comms'       => $validated['img_rights_web'],
                'auth_image_rights'    => $validated['img_rights_rss'],
                'privacy_policy'       => $validated['privacy_policy'],
                'token'                => $ticketToken,
                'cancelled_at'         => null,
                'attendance'           => false,
                'checked_in_at'        => null,
                'verification_code_id' => null,
            ];

            if ($cancelledRow) {
                DB::table('attendee_edition')
                    ->where('edition_id', $edition->id)
                    ->where('attendee_id', $attendee->id)
                    ->update($pivotData);
            } else {
                $edition->attendees()->attach($attendee->id, $pivotData);
            }

            $qr = QrCode::format('png')->size(300)->generate($ticketToken);
            $ticketImage = $this->buildTicketImage($qr, $edition);
            Storage::disk('public')->put('tickets/' . $ticketToken . '.png', $ticketImage);

            AttendeeEditionSignUpEvent::dispatch($edition, $attendee, $ticketToken);

            return ['attendee' => $attendee];
        });
    }
    private function buildTicketImage(string $qrPng, Edition $edition): string
    {
        $qr = new \Imagick();
        $qr->readImageBlob($qrPng);
        $qrSize = $qr->getImageWidth();

        $lines = [
            ['font' => resource_path('fonts/DejaVuSans-Bold.ttf'), 'size' => 20, 'text' => $edition->event->name],
            ['font' => resource_path('fonts/DejaVuSans.ttf'), 'size' => 16, 'text' => 'Edición #' . $edition->id],
            ['font' => resource_path('fonts/DejaVuSans.ttf'), 'size' => 16, 'text' => $edition->date->format('d/m/Y H:i')],
        ];

        $padding = 20;
        $lineSpacing = 8;

        $draw = new \ImagickDraw();
        $draw->setTextAlignment(\Imagick::ALIGN_CENTER);
        $draw->setFillColor(new \ImagickPixel('#111827'));

        $metrics = [];
        $maxTextWidth = 0;
        $textAreaHeight = $padding;
        foreach ($lines as $line) {
            $draw->setFont($line['font']);
            $draw->setFontSize($line['size']);
            $lineMetrics = $qr->queryFontMetrics($draw, $line['text']);
            $metrics[] = $lineMetrics;
            $maxTextWidth = max($maxTextWidth, $lineMetrics['textWidth']);
            $textAreaHeight += $lineMetrics['textHeight'] + $lineSpacing;
        }
        $textAreaHeight += $padding;

        $canvasWidth = (int) max($qrSize, $maxTextWidth + $padding * 2);
        $canvasHeight = (int) ($textAreaHeight + $qrSize + $padding);

        $canvas = new \Imagick();
        $canvas->newImage($canvasWidth, $canvasHeight, new \ImagickPixel('white'), 'png');

        $y = $padding;
        foreach ($lines as $i => $line) {
            $draw->setFont($line['font']);
            $draw->setFontSize($line['size']);
            $y += $metrics[$i]['ascender'];
            $canvas->annotateImage($draw, $canvasWidth / 2, $y, 0, $line['text']);
            $y += -$metrics[$i]['descender'] + $lineSpacing;
        }

        $qrX = (int) (($canvasWidth - $qrSize) / 2);
        $canvas->compositeImage($qr, \Imagick::COMPOSITE_OVER, $qrX, (int) $textAreaHeight);
        $canvas->setImageFormat('png');

        $output = $canvas->getImageBlob();

        $canvas->clear();
        $qr->clear();

        return $output;
    }
}
