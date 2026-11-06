<?php

namespace Tests\Unit;

use App\Models\Edition;
use App\Models\EditionReminder;
use PHPUnit\Framework\TestCase;

class EditionReminderTest extends TestCase
{
    public function test_reminder_of_a_cancelled_edition_is_never_due_and_does_not_crash(): void
    {
        $reminder = new EditionReminder(['days_before' => 1]);
        // belongsTo excluye las ediciones soft-deleted, así que la relación llega a null.
        $reminder->setRelation('edition', null);

        $this->assertFalse($reminder->isDue());
    }

    public function test_reminder_is_due_once_days_before_has_been_reached(): void
    {
        $edition = new Edition();
        $edition->date = now()->addHours(12);

        $reminder = new EditionReminder(['days_before' => 1]);
        $reminder->setRelation('edition', $edition);

        $this->assertTrue($reminder->isDue());
    }
}
