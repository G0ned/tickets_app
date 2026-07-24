<?php

namespace App\Exports;

use App\Models\Edition;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendeesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Edition $edition)
    {
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
            'Politica de privacidad', 'Asistió', 'Hora de entrada',
        ];
    }

    public function map($attendee): array
    {
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
        ];
    }
}
