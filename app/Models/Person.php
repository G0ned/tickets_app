<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Type;


class Person extends Model
{
    use SoftDeletes;

    protected $table = 'person';

    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'passport',
        'type',
        'brand',
        'client_portfolio_id'
    ];

    protected function casts(): array {
        return [
            'type' => Type::class
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this ->belongsTo(ClientPortfolio::class, 'client_portfolio_id');
    }

    public function edition(): BelongsToMany
    {
        return $this->belongsToMany(Edition::class, 'attendee_edition', 'attendee_id', 'edition_id')->withPivot(
            'auth_for_ad', 'auth_for_comms', 'auth_image_rights', 'privacy_policy', 'attendance', 'checked_in_at', 'verification_code_id'
        );
    }
}
