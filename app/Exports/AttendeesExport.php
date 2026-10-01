<?php

namespace App\Exports;

use App\Models\Edition;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendeesExport implements FromCollection, WithHeadings, WithMapping
{
    private Collection $inviterNames;

    public function __construct(private Edition $edition)
    {
        $this->inviterNames = $edition->guestInviterNames();
    }

    public function collection(): Collection
    {
        return $this->edition->attendees;
    }

    public function headings(): array
    {
        return [
            'Evento', 'ID edicion', 'Nombre', 'Apellidos', 'Identificación', 'e-mail', 'Teléfono',
            'Derechos para publicidad', 'Derechos para comunicaciones', 'Derechos de imagen',
            'Politica de privacidad', 'Asistió', 'Hora de entrada', 'Invitado', 'Invitado por',
        ];
    }

    public function map($attendee): array
    {
        $invitedBy = $attendee->pivot->is_guest
            ? ($this->inviterNames[$attendee->pivot->verification_code_id] ?? '-')
            : '-';

        return [
            $this->edition->event->name,
            $this->edition->id,
            $attendee->name,
            $attendee->surname,
            $attendee->passport,
            $attendee->email,
            $attendee->phone,
            $attendee->pivot->auth_for_ad ? 'Si' : 'No',
            $attendee->pivot->auth_for_comms ? 'Si' : 'No',
            $attendee->pivot->auth_image_rights ? 'Si' : 'No',
            $attendee->pivot->privacy_policy ? 'Si' : 'No',
            $attendee->pivot->attendance ? 'Si' : 'No',
            $attendee->pivot->checked_in_at ? \Carbon\Carbon::parse($attendee->pivot->checked_in_at)->format('d/m/Y H:i') : '-',
            $attendee->pivot->is_guest ? 'Si' : 'No',
            $invitedBy,
        ];
    }
}
