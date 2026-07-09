<?php

namespace Tests\Feature;

use App\Events\AttendeeEditionSignUpEvent;
use App\Listeners\SendTicketMail;
use App\Mail\AttendeeTicketMail;
use App\Models\Edition;
use App\Models\Person;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SendTicketMailListenerTest extends TestCase
{
    public function test_it_sends_ticket_email_when_event_is_handled(): void
    {
        Mail::fake();
        Storage::fake('public');

        $edition = new Edition();
        $edition->id = 7;
        $edition->event = new class {
            public string $name = 'Demo Event';
        };

        $attendee = new Person();
        $attendee->name = 'Jane';
        $attendee->surname = 'Doe';
        $attendee->email = 'jane@example.com';

        $token = 'sample-token';
        Storage::disk('public')->put('tickets/' . $token . '.png', 'fake-qr');

        $listener = new SendTicketMail();
        $listener->handle(new AttendeeEditionSignUpEvent($edition, $attendee, $token));

        Mail::assertSent(AttendeeTicketMail::class, function (AttendeeTicketMail $mail) use ($attendee, $edition, $token): bool {
            return $mail->hasTo($attendee->email)
                && $mail->edition->id === $edition->id
                && $mail->attendee->email === $attendee->email
                && $mail->token === $token;
        });
    }
}
