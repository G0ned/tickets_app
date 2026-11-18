<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'zip_code',
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

    public const LIST_SORTABLE_COLUMNS = ['name', 'surname', 'email', 'phone', 'type', 'brand'];
    public function scopeForListing(Builder $query, string $sort, string $direction, ?string $type, ?string $brand)
    {
        return $query
            ->with('portfolio')
            ->withExists([
                'allEditionRegistrations as has_live_guest_registration' => fn ($q) => $q->withTrashed()->where('attendee_edition.is_guest', true),
                'editionRegistrationHistory as has_archived_guest_registration' => fn ($q) => $q->where('is_guest', true),
            ])
            ->when($type, fn ($q, $type) => $q->where('type', $type))
            ->when($brand, fn ($q, $brand) => $q->where('brand', 'like', "%{$brand}%"))
            ->orderBy($sort, $direction)
            ->when(in_array($sort, ['name', 'surname'], true), function ($q) use ($sort, $direction) {
                $q->orderBy($sort === 'name' ? 'surname' : 'name', $direction);
            });
    }

    public static function flagGuests($people)
    {
        return $people->each(function (Person $person) {
            $person->has_been_guest = $person->has_live_guest_registration || $person->has_archived_guest_registration;
        });
    }

    public function portfolio(): BelongsTo
    {
        return $this ->belongsTo(ClientPortfolio::class, 'client_portfolio_id');
    }

    public function edition(): BelongsToMany
    {
        return $this->belongsToMany(Edition::class, 'attendee_edition', 'attendee_id', 'edition_id')->withPivot(
            'token', 'auth_for_ad', 'auth_for_comms', 'auth_image_rights', 'privacy_policy', 'attendance', 'checked_in_at', 'verification_code_id', 'cancelled_at', 'is_guest'
        )->withTimestamps()->wherePivotNull('cancelled_at');
    }

    public function allEditionRegistrations(): BelongsToMany
    {
        return $this->belongsToMany(Edition::class, 'attendee_edition', 'attendee_id', 'edition_id')->withPivot(
            'token', 'auth_for_ad', 'auth_for_comms', 'auth_image_rights', 'privacy_policy', 'attendance', 'checked_in_at', 'verification_code_id', 'cancelled_at', 'is_guest'
        )->withTimestamps();
    }

    /**
     * Archived registrations (AttendeeEditionHistory) left behind whenever an
     * edition this person registered for got reactivated - at that point the row
     * moves out of attendee_edition entirely, so allEditionRegistrations() alone
     * can't see it anymore. Needed alongside it to answer "was this person ever
     * a guest", not just "is there a live row saying so right now".
     */
    public function editionRegistrationHistory(): HasMany
    {
        return $this->hasMany(AttendeeEditionHistory::class, 'attendee_id');
    }
}
