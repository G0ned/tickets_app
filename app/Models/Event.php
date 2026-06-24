<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'public',
        'poster_path',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function editions(): HasMany
    {
        return $this->hasMany(Edition::class, 'event_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');    
    }

    public function organizers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_organizer', 'event_id', 'user_id');
    }
}
