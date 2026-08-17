<?php

namespace App\Mail;

use App\Models\Edition;
use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EditionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Edition $edition,
        public Person $attendee,
        public int $daysBefore,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio: ' . $this->edition->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.reminder_msg',
        );
    }
}
