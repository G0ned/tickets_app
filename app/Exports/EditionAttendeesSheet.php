<?php

namespace App\Exports;

use App\Models\Edition;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class EditionAttendeesSheet implements FromArray, WithTitle
{
    private AttendeesExport $attendees;

    public function __construct(private Edition $edition)
    {
        $edition->loadMissing('event');
        $edition->load('attendees');
        $this->attendees = new AttendeesExport($edition);
    }

    public function array(): array
    {
        $rows = [
            ['ID de la edición', $this->edition->id],
            ['Fecha', $this->edition->date->format('d/m/Y')],
            ['Hora', $this->edition->date->format('H:i')],
            [],
            $this->attendees->headings(),
        ];

        foreach ($this->attendees->collection() as $attendee) {
            $rows[] = $this->attendees->map($attendee);
        }

        return $rows;
    }

    public function title(): string
    {
        // Sheet names: max 31 chars and no \ / ? * [ ] :
        return "Edición {$this->edition->id} - " . $this->edition->date->format('d-m-Y');
    }
}
