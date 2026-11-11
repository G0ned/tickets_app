<?php

namespace App\Console\Commands;

use App\Mail\EditionSurveyMail;
use App\Models\Edition;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

#[Signature('app:send-edition-surveys')]
#[Description('Sends the satisfaction survey link to attendees 30 minutes before an edition ends')]
class SendEditionSurveys extends Command
{
    /** Minutes before the end of the edition at which the survey is sent. */
    private const MINUTES_BEFORE_END = 30;

    /** Editions that ended longer ago than this are skipped (e.g. after downtime or first deploy). */
    private const MAX_HOURS_AFTER_END = 24;

    public function handle(): void
    {
        $editions = Edition::whereNull('survey_sent_at')
            ->whereHas('event', fn ($q) => $q->whereNotNull('survey_url')->where('survey_url', '!=', ''))
            ->with(['event', 'attendees'])
            ->get()
            ->filter(function (Edition $edition) {
                $end = $edition->date->copy()->addMinutes((int) round($edition->duration * 60));

                return now()->greaterThanOrEqualTo($end->copy()->subMinutes(self::MINUTES_BEFORE_END))
                    && now()->lessThanOrEqualTo($end->copy()->addHours(self::MAX_HOURS_AFTER_END));
            });

        $sent = 0;
        $failed = 0;

        foreach ($editions as $edition) {
            try {
                foreach ($edition->attendees as $attendee) {
                    Mail::to($attendee->email)->queue(
                        new EditionSurveyMail($edition, $attendee, $edition->event->survey_url)
                    );
                    $sent++;
                }

                $edition->forceFill(['survey_sent_at' => now()])->save();
            } catch (Throwable $e) {
                $failed++;
                $this->error("Edición {$edition->id}: {$e->getMessage()}");
            }
        }

        $this->info(sprintf(
            '%d edición(es) procesada(s), %d correo(s) encolado(s), %d fallida(s).',
            $editions->count(),
            $sent,
            $failed
        ));
    }
}
