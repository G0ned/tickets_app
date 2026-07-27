<?php

namespace App\Mail;

use App\Models\Edition;
use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Edition $edition,
        public Person $person,
        public string $token,
        public int $allowedRegistrations,
        public Collection $verificationCodes,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitación: ' . $this->edition->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.invitation_msg',
        );
    }
}
