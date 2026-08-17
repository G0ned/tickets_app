<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditionReminder extends Model
{
    protected $fillable = [
        'edition_id',
        'days_before',
        'created_by',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    /**
     * True once "days_before" days remain until the edition's start (or
     * fewer - a reminder that's overdue, e.g. the scheduler was down, still
     * fires once it catches up). Only ever true for a reminder not yet sent.
     */
    public function isDue(): bool
    {
        if ($this->isSent()) {
            return false;
        }

        return now()->greaterThanOrEqualTo($this->edition->date->copy()->subDays($this->days_before));
    }
}
