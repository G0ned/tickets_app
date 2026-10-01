<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Edition extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'event_id',
        'date',
        'duration',
        'location',
        'capacity',
        'status',
        'registration_deadline',
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
            'token', 'auth_for_ad', 'auth_for_comms', 'auth_image_rights', 'privacy_policy', 'attendance', 'checked_in_at', 'verification_code_id', 'cancelled_at', 'is_guest'
        )->wherePivotNull('cancelled_at');
    }

    public function attendeeRegistrations(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'attendee_edition', 'edition_id', 'attendee_id')->withPivot(
            'token', 'auth_for_ad', 'auth_for_comms', 'auth_image_rights', 'privacy_policy', 'attendance', 'checked_in_at', 'verification_code_id', 'cancelled_at', 'is_guest'
        );
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(EditionReminder::class);
    }

    public function hasEnded(): bool
    {
        return now() > $this->date->copy()->addHours($this->duration);
    }

    public function registrationClosed(): bool
    {
        return $this->registration_deadline !== null && now() > $this->registration_deadline;
    }

    public function guestInviterNames(): Collection
    {
        $codeIds = $this->relationLoaded('attendees')
            ? $this->attendees->pluck('pivot.verification_code_id')->filter()->unique()->values()
            : collect();

        if ($codeIds->isEmpty()) {
            return collect();
        }

        return VerificationCode::with('person')
            ->whereIn('id', $codeIds)
            ->get()
            ->filter(fn (VerificationCode $code) => $code->person !== null)
            ->mapWithKeys(fn (VerificationCode $code) => [
                $code->id => trim($code->person->name . ' ' . $code->person->surname),
            ]);
    }

    protected $casts = [
        'date'                   => 'datetime',
        'duration'               => 'float',
        'registration_deadline'  => 'datetime',
    ];
}
