<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Type;

class Person extends Model
{
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
}
