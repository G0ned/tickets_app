<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\ClientPortfolio;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'is_admin',
        'is_supervisor'
    ]; 

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function isSupervisor(): bool
    {
        return $this->is_supervisor;
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function organized_events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_organizer', 'user_id', 'event_id')->withPivot('assigned_by', 'assigned_at');
    }

    public function managed_events(): BelongsToMany
    {
        return $this->belongsToMany(Edition::class, 'manager_edition', 'manager_id', 'edition_id')->withPivot(
            'is_supervisor', 'is_doorman', 'invitations_capacity'
        );
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(ClientPortfolio::class, 'user_id');
    }
}
