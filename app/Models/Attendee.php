<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Assistant extends Model
{
    protected $fillable = [
        'invitation_list_id',
        'person_id',
        'name',
        'surname',
        'email',
        'phone',
        'passport',
        'ad_notifications',
        'communication_notifications',
        'image_authorization',
    ];

    public function invitationLists(): BelongsToMany
    {
        return $this->belongsToMany(InvitationList::class, 'attendee_invitation_list', 'attendee_id', 'invitation_list_id');
    }

    public function eventEdition(): BelongsToMany
    {
        return $this->belongstoMany(Edition::class, 'attendee_edition');
    }
}
