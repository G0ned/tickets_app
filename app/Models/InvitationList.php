<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationList extends Model
{
    protected $fillable = [
        'name',
        'client_portfolio_id',
        'user_edition_id'
    ];

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'invitation_list_person', 'invitation_list_id', 'person_id');
    }

    public function clientPorfolio(): BelongsTo
    {
        return $this->belongsTo(ClientPortfolio::class, 'client_portfolio_id');
    }

    public function userEdition(): BelongsTo
    {
        return $this->belongsTo('manager_edition', 'id');
    }
}
