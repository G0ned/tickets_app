<?php

namespace App\Console\Commands;

use App\Mail\EditionReminderMail;
use App\Models\EditionReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:send-edition-reminders')]
#[Description('Sends due edition reminder emails to attendees who consented to communications')]
class SendEditionReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $dueReminders = EditionReminder::whereNull('sent_at')
            ->with(['edition.event', 'edition.attendees'])
            ->get()
            ->filter(fn (EditionReminder $reminder) => $reminder->isDue());

        $sentCount = 0;

        foreach ($dueReminders as $reminder) {
            $edition = $reminder->edition;

            foreach ($edition->attendees as $attendee) {
                if (!$attendee->pivot->auth_for_comms) {
                    continue;
                }

                Mail::to($attendee->email)->queue(
                    new EditionReminderMail($edition, $attendee, $reminder->days_before)
                );
                $sentCount++;
            }

            $reminder->update(['sent_at' => now()]);
        }

        $this->info(sprintf(
            '%d recordatorio(s) procesado(s), %d correo(s) encolado(s).',
            $dueReminders->count(),
            $sentCount
        ));
    }
}
