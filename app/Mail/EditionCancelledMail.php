<?php

namespace App\Mail;

use App\Models\Edition;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EditionCancelledMail extends Mailable
{
    use Queueable;

    public string $eventName;

    public string $attendeeName;

    public Carbon $date;

    public ?string $location;

    /**
     * Guarda solo valores sueltos y no los modelos: la edición ya está
     * soft-deleted cuando el worker procesa el job y SerializesModels no
     * recupera modelos borrados (ModelNotFoundException).
     */
    public function __construct(Edition $edition, Person $attendee)
    {
        $this->eventName = $edition->event->name;
        $this->attendeeName = trim($attendee->name . ' ' . $attendee->surname);
        $this->date = $edition->date->copy();
        $this->location = $edition->location;
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
