<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendeeEditionHistory extends Model
{
    public $timestamps = false;

    protected $table = 'attendee_edition_history';

    protected $fillable = [
        'edition_id',
        'attendee_id',
        'token',
        'auth_for_ad',
        'auth_for_comms',
        'auth_image_rights',
        'privacy_policy',
        'attendance',
        'checked_in_at',
        'verification_code_id',
        'is_guest',
        'registered_at',
        'archived_at',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'registered_at' => 'datetime',
        'archived_at'   => 'datetime',
    ];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class, 'edition_id');
    }

    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'attendee_id')->withoutGlobalScope(SoftDeletingScope::class);
    }

    public function verificationCode(): BelongsTo
    {
        return $this->belongsTo(VerificationCode::class, 'verification_code_id');
    }
}
