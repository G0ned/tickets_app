<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EventAttendeesExport implements WithMultipleSheets
{
    public function __construct(private Event $event)
    {
    }

    public function sheets(): array
    {
        return $this->event->editions
            ->sortBy('date')
            ->map(fn ($edition) => new EditionAttendeesSheet($edition))
            ->values()
            ->all();
    }
}
