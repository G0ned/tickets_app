<?php

namespace App\Mail;

use App\Models\Edition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EditionRestoredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Edition $edition, public User $manager)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Edición reactivada: ' . $this->edition->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.edition_restored',
        );
    }
}
