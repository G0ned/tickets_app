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
        'edition_id'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'invitation_list_person', 'invitation_list_id', 'person_id')
            ->withPivot('allowed_registrations', 'token', 'registrations_used')
            ->using(InvitationListPerson::class);
    }

    public function clientPorfolio(): BelongsTo
    {
        return $this->belongsTo(ClientPortfolio::class, 'client_portfolio_id');
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class, 'edition_id');
    }

    public function invitationsCapacity(): ?int
    {
        return $this->edition
            ?->managers()
            ->where('manager_id', $this->clientPorfolio->user_id)
            ->first()
            ?->pivot
            ?->invitations_capacity;
    }

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }
}
