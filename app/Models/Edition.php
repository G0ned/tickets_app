<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edition extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'event_id',
        'date',
        'duration',
        'location',
        'capacity',
        'status'
    ];

    public function event():BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    } 

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'manager_edition', 'edition_id', 'manager_id')->withPivot(
            'is_supervisor', 'is_doorman', 'invitations_capacity'
        );
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'attendee_edition', 'edition_id', 'attendee_id')->withPivot(
            'token', 'auth_for_ad', 'auth_for_comms', 'auth_image_rights', 'privacy_policy', 'attendance', 'checked_in_at', 'verification_code_id'
        );
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(EditionReminder::class);
    }

    protected $casts = [
        'date'     => 'datetime',
        'duration' => 'float',
    ];
}
