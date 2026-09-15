<?php

namespace App\Mail;

use App\Models\Edition;
use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EditionCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Edition $edition, public Person $attendee)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cancelación: ' . $this->edition->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.edition_cancelled',
        );
    }
}
