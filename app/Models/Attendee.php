<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'zip_code',
        'privacy_policy',
        'img_rights_ads',
        'img_rights_web',
        'img_rights_rss'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function events(){
        return $this-> belongsToMany(Events::class, 'attendee_event', 'id_attendees', 'id_event');
    }
}
