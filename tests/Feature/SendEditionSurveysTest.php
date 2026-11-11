<?php

namespace Tests\Feature;

use App\Mail\EditionSurveyMail;
use App\Models\Edition;
use App\Models\Event;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendEditionSurveysTest extends TestCase
{
    use RefreshDatabase;

    private function makeEdition(?string $surveyUrl, $start, float $duration = 2): Edition
    {
        $user = User::create([
            'name' => 'John', 'surname' => 'Doe', 'email' => 'john' . uniqid() . '@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => false, 'is_supervisor' => false,
        ]);

        $event = Event::create([
            'name' => 'Demo', 'description' => 'Desc', 'public' => false,
            'poster_path' => 'p.jpg', 'created_by' => $user->id, 'survey_url' => $surveyUrl,
        ]);

        return Edition::create([
            'event_id' => $event->id, 'date' => $start, 'duration' => $duration,
            'location' => 'Sala', 'capacity' => 10, 'status' => true,
        ]);
    }

    private function addAttendee(Edition $edition, string $email, bool $cancelled = false): void
    {
        $person = Person::create(['name' => 'Ana', 'surname' => 'Lopez', 'email' => $email, 'phone' => (string) random_int(600000000, 699999999), 'passport' => uniqid()]);
        $edition->attendeeRegistrations()->attach($person->id, [
            'token' => bin2hex(random_bytes(8)),
            'cancelled_at' => $cancelled ? now() : null,
        ]);
    }

    public function test_sends_survey_30_minutes_before_end_once(): void
    {
        Mail::fake();
        // Starts 1h31m ago, lasts 2h -> ends in 29 min.
        $edition = $this->makeEdition('https://survey.test/x', now()->subMinutes(91));
        $this->addAttendee($edition, 'a@example.test');
        $this->addAttendee($edition, 'b@example.test', cancelled: true);

        $this->artisan('app:send-edition-surveys')->assertSuccessful();
        $this->artisan('app:send-edition-surveys')->assertSuccessful();

        Mail::assertQueued(EditionSurveyMail::class, 1);
        Mail::assertQueued(EditionSurveyMail::class, fn ($m) => $m->hasTo('a@example.test') && $m->surveyUrl === 'https://survey.test/x');
        $this->assertNotNull($edition->fresh()->survey_sent_at);
    }

    public function test_does_not_send_before_window_or_without_survey_url(): void
    {
        Mail::fake();
        // Ends in 31 min -> too early.
        $early = $this->makeEdition('https://survey.test/x', now()->subMinutes(89));
        $this->addAttendee($early, 'a@example.test');
        // Within window but event has no survey.
        $noSurvey = $this->makeEdition(null, now()->subMinutes(91));
        $this->addAttendee($noSurvey, 'c@example.test');

        $this->artisan('app:send-edition-surveys')->assertSuccessful();

        Mail::assertNothingQueued();
    }
}
