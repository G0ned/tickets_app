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

    /**
     * Captured up front, not read lazily via $edition->event->name: when this
     * mail was queued from a whole-event cancellation (EventController::cancel()),
     * the event itself gets soft-deleted right after every edition is queued for
     * cancellation. By the time the queued job actually runs, Laravel re-fetches
     * $edition fresh and reloads its event relation - which, being scoped by
     * Event's own SoftDeletes, now resolves to null, crashing the job. A plain
     * string has no such relation to lose on the way through the queue.
     */
    public string $eventName;

    public function __construct(public Edition $edition, public Person $attendee)
    {
        $this->eventName = $edition->event->name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cancelación: ' . $this->eventName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.edition_cancelled',
        );
    }
}
