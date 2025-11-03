<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'location',
        'date',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assistants()
    {
        return $this->belongsToMany(Attendee::class, 'attendee_event', 'id_attendees', 'id_event');
    }
}
