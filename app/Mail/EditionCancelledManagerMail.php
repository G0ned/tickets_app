<?php

namespace App\Mail;

use App\Models\Edition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EditionCancelledManagerMail extends Mailable
{
    use Queueable;

    public string $eventName;

    public string $managerName;

    public Carbon $date;

    public ?string $location;

    /**
     * Guarda solo valores sueltos y no los modelos: la edición ya está
     * soft-deleted cuando el worker procesa el job y SerializesModels no
     * recupera modelos borrados (ModelNotFoundException).
     */
    public function __construct(Edition $edition, User $manager)
    {
        $this->eventName = $edition->event->name;
        $this->managerName = trim($manager->name . ' ' . $manager->surname);
        $this->date = $edition->date->copy();
        $this->location = $edition->location;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Edición cancelada: ' . $this->eventName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.edition_cancelled_manager',
        );
    }
}
