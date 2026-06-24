<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class ClientPortfolio extends Model
{
    protected $table = 'client_portfolio';

    protected $fillable = [
        'name',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this -> belongsTo(User::class, 'user_id');
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class, 'client_portfolio_id');
    }
}
