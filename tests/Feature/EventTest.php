<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Event;
use App\Models\User;
use App\Models\Edition;

class EventTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Bob',
            'surname' => 'Reynolds',
            'email' => 'bobreyn@example.test',
            'password' => bcrypt('bobreyn1234'),
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

    public function test_created_event_is_assigned_to_user(): void
    { 
        $this->assertTrue($this->event->createdBy->is($this->user));
    }

     public function test_user_has_not_created_event(): void
    {
        $other_user = User::create([
            'name' => 'James',
            'surname' => 'Burns',
            'email' => 'jameburn@example.test',
            'password' => bcrypt('jameburn1234'),
            'is_admin' => false,
            'is_supervisor' => false
        ]);

        $this->assertFalse($this->event->createdBy->is($other_user));
    }

    public function test_event_has_many_editions(): void
    {

        $editions = [
            $edition1 = Edition::create([
                'event_id' => $this->event->id,
                'date' => '9999-12-30 00:00:00',
                'duration' => 1,
                'location' => 'demoPlace',
                'capacity' => 999,
                'status' => false

            ]),

            $edition2 = Edition::create([
                'event_id' => $this->event->id,
                'date' => '9999-12-29 00:00:00',
                'duration' => 1,
                'location' => 'demoPlace',
                'capacity' => 999,
                'status' => false

            ]),

            $edition3 = Edition::create([
                'event_id' => $this->event->id,
                'date' => '9999-12-30 08:00:00',
                'duration' => 1,
                'location' => 'demoPlace',
                'capacity' => 999,
                'status' => false

            ]),
        ];
        foreach($editions as $edition){
            $this->assertTrue($this->event->editions->contains($edition));
        }
        $this->assertCount(3, $this->event->editions);
    }

    public function test_event_does_not_have_edition(): void
    {
        $other_event = Event::create([
            'name' => 'DemoName',
            'description' => 'DemoDescription',
            'public' => false,
            'poster_path' => 'path/to/poster_file.jpeg',
            'created_by' => $this->user->id,
            'updated_by' => null,
            'deleted_by' => null]);
    
        $edition = Edition::create([
            'event_id' => $other_event->id,
            'date' => '9999-12-30 08:00:00',
            'duration' => 1,
            'location' => 'demoPlace',
            'capacity' => 999,
            'status' => false

        ]);

        $this->assertFalse($this->event->editions->contains($edition));
    }
}
