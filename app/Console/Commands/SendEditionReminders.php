<?php

namespace App\Console\Commands;

use App\Mail\EditionReminderMail;
use App\Models\EditionReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

#[Signature('app:send-edition-reminders')]
#[Description('Sends due edition reminder emails to attendees who consented to communications')]
class SendEditionReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $dueReminders = EditionReminder::whereNull('sent_at')
                ->with(['edition.event', 'edition.attendees'])
                ->get()
                ->filter(fn (EditionReminder $reminder) => $reminder->isDue());
        } catch (Throwable $e) {
            EditionReminder::whereNull('sent_at')->update(['last_error' => 'Fallo al cargar recordatorios: ' . $e->getMessage()]);
            $this->error('Fallo al cargar los recordatorios: ' . $e->getMessage());
            return;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($dueReminders as $reminder) {
            try {
                $edition = $reminder->edition;

                foreach ($edition->attendees as $attendee) {
                    if (!$attendee->pivot->auth_for_comms) {
                        continue;
                    }

                    Mail::to($attendee->email)->queue(
                        new EditionReminderMail($edition, $attendee, $reminder->days_before, $attendee->pivot->token)
                    );
                    $sentCount++;
                }

                $reminder->update(['sent_at' => now(), 'last_error' => null]);
            } catch (Throwable $e) {
                $failedCount++;
                $reminder->update(['last_error' => $e->getMessage()]);
            }
        }

        $this->info(sprintf(
            '%d recordatorio(s) procesado(s), %d correo(s) encolado(s), %d fallido(s).',
            $dueReminders->count(),
            $sentCount,
            $failedCount
        ));
    }
}
