<?php

namespace App\Mail;

use App\Models\Edition;
use App\Models\InvitationList;
use App\Models\Person;
use App\Models\VerificationCode;
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

    public ?string $salesEmail;

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
        $this->salesEmail = $this->resolveSalesEmail($edition, $attendee);
    }

    /**
     * Email del comercial que gestionó las invitaciones del asistente, por orden:
     *  1. Código de verificación con el que se registró (clientes invitados e
     *     invitados de un cliente): código -> lista de invitación -> cartera -> usuario.
     *  2. Lista de invitación de esta edición en la que figura el asistente.
     *  3. Cartera del propio cliente (Person -> cartera -> usuario).
     */
    private function resolveSalesEmail(Edition $edition, Person $attendee): ?string
    {
        $codeId = $attendee->pivot?->verification_code_id;

        if ($codeId) {
            $email = VerificationCode::with('invitationList.clientPorfolio.user')
                ->find($codeId)
                ?->invitationList?->clientPorfolio?->user?->email;

            if ($email) {
                return $email;
            }
        }

        $email = InvitationList::with('clientPorfolio.user')
            ->where('edition_id', $edition->id)
            ->whereHas('persons', fn ($q) => $q->where('person.id', $attendee->id))
            ->first()
            ?->clientPorfolio?->user?->email;

        return $email ?? $attendee->portfolio?->user?->email;
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
