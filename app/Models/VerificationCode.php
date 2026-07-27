<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationCode extends Model
{
    protected $fillable = [
        'invitation_list_id',
        'person_id',
        'edition_id',
        'code',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function invitationList(): BelongsTo
    {
        return $this->belongsTo(InvitationList::class, 'invitation_list_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class, 'edition_id');
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    /**
     * Generates a code from an alphabet with visually ambiguous characters
     * (0/O, 1/I/L) removed, since these are read off an email and typed
     * back in by hand during registration.
     */
    public static function generateUnique(): string
    {
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $code = collect(range(1, 8))
                ->map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)])
                ->implode('');
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
