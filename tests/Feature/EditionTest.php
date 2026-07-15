<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Event;
use App\Models\Edition;
use App\Models\User;
use App\Models\Person;

class EditionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'johndoe@example.test',
            'password' => bcrypt('johndoe1234'),
            'is_admin' => false,
            'is_supervisor' => false
        ]);

        $this->event = Event::create([
            'name' => 'DemoName',
            'description' => 'DemoDescription',
            'public' => false,
            'poster_path' => 'path/to/poster_file.jpeg',
            'created_by' => $this->user->id,
            'updated_by' => null,
            'deleted_by' => null
        ]);

    }

    public function test_edition_has_event(): void
    {
        $edition = Edition::create([
            'event_id' => $this->event->id,
            'date' => '2026-08-10 09:00:00',
            'location' => 'DemoPlace',
            'duration' => 9999,
            'capacity' => 50,
            'status' => false
        ]);

        $this->assertTrue($edition->event()->is($this->event));
    }

      public function test_edition_does_not_belong_to_event(): void
    {
        $other_event = Event::create([
            'name' => 'DemoName',
            'description' => 'DemoDescription',
            'public' => false,
            'poster_path' => 'path/to/poster_file.jpeg',
            'created_by' => $this->user->id,
            'updated_by' => null,
            'deleted_by' => null
        ]);

        $edition = Edition::create([
            'event_id' => $other_event->id,
            'date' => '2026-08-10 09:00:00',
            'location' => 'DemoPlace',
            'duration' => 9999,
            'capacity' => 50,
            'status' => false
        ]);

        $this->assertTrue($edition->event()->is($other_event));
    }
}
