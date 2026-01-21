<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'location',
        'date',
        'user_id',
        'is_active',
        'capacity'
    ];

    protected $casts = [
        'date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'identification');
    }

    public function assistants()
    {
        return $this->belongsToMany(Attendee::class, 'attendees_events', 'id_event', 'id_attendees')->withCasts([
            'id_attendees' => 'string',
        ])->withPivot('has_attended', 'checked_in_at');
    }
}
