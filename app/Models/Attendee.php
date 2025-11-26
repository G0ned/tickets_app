<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{

    public $timestamps = false;

    public $keyType = 'string';

    public $incrementing = false;

    protected $primaryKey = 'user_id';

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
        return $this->belongsTo(User::class, 'user_id', 'identification'); //Specify which keys are used in the relationship (current model - reference model) to avoide nullable reference errors. 
    }

    public function events(){
        return $this-> belongsToMany(Event::class, 'attendees_events', 'id_attendees', 'id_event')->withPivot("has_attended", "checked_in_at");
    }
}
