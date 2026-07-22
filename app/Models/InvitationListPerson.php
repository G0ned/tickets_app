<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InvitationListPerson extends Pivot
{
    protected $table = 'invitation_list_person';

    public $incrementing = false;

    protected $fillable = [
        'allowed_registrations',
        'token',
        'registrations_used',
    ];

    public function invitationList(): BelongsTo
    {
        return $this->belongsTo(InvitationList::class, 'invitation_list_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function remainingRegistrations(): int
    {
        return max(0, $this->allowed_registrations - $this->registrations_used);
    }
}
