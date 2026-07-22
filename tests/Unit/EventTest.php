<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Event;
use App\Models\User;

class EventTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    public function test_event_is_public(): void
    {
        //Arrange
        $event = new Event();
        $event->public = true;

        //Assert
        $this->assertTrue($event->public = true);
    }

     public function test_event_is_not_public(): void
    {
        //Arrange
        $event = new Event();
        $event->public = false;

        //Assert
        $this->assertFalse($event->public = false);
    }
}
