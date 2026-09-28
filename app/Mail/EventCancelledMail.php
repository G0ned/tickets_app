<?php

namespace App\Mail;

use App\Models\Edition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Captured up front, not read lazily via $edition->event->name - see
     * EditionCancelledMail's identical property for the full reasoning. It
     * applies even more directly here: this mail exists specifically because
     * the event got soft-deleted, so by the time its queued job runs, the
     * event relation is guaranteed to no longer resolve.
     */
    public string $eventName;

    public function __construct(public Edition $edition, public User $manager)
    {
        $this->eventName = $edition->event->name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Evento cancelado: ' . $this->eventName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.event_cancelled',
        );
    }
}
