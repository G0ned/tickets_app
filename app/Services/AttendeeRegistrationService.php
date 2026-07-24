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
            $ticketImage = $this->buildTicketImage($qr, $edition);
            Storage::disk('public')->put('tickets/' . $ticketToken . '.png', $ticketImage);

            AttendeeEditionSignUpEvent::dispatch($edition, $attendee, $ticketToken);

            return ['attendee' => $attendee];
        });
    }

    /**
     * Composes the raw QR png with a header showing the event name, edition
     * number and date/time, so the ticket is identifiable without scanning it.
     *
     * Uses Imagick rather than GD: the project's GD build has no FreeType
     * support (imagettftext/imagettfbbox are unavailable), while Imagick's
     * ImageMagick delegate renders TTF text natively.
     */
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
