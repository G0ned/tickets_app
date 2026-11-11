<?php

namespace App\Mail;

use App\Models\Edition;
use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EditionSurveyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Edition $edition,
        public Person $attendee,
        public string $surveyUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cuéntanos qué tal ha ido: ' . $this->edition->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.survey_msg',
        );
    }
}
