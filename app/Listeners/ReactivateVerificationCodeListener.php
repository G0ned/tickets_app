<?php

namespace App\Listeners;

use App\Events\AttendeeEditionCancelAssistance;
use App\Models\VerificationCode;

class ReactivateVerificationCodeListener
{
    /**
     * Frees up the verification code consumed by this registration, if any
     * (public sign-ups have none), so it can be validated and used again -
     * same rules as any other unused code.
     */
    public function handle(AttendeeEditionCancelAssistance $event): void
    {
        if ($event->verificationCodeId === null) {
            return;
        }

        VerificationCode::where('id', $event->verificationCodeId)->update(['used_at' => null]);
    }
}
