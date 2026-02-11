<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'sci_id',
        'name',
        'phone',
        'phone_secondary',
        'email',
        'category',
        'custom_category',
        'specialty',
        'company',
        'address',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(ProviderContract::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                          */
    /* ------------------------------------------------------------------ */

    public function getCategoryLabelAttribute(): string
    {
        if ($this->category === 'autre' && $this->custom_category) {
            return $this->custom_category;
        }

        return match ($this->category) {
            'artisan'      => 'Artisan',
            'manoeuvre'    => 'Manoeuvre',
            'plombier'    => 'Plombier',
            'electricien' => 'Electricien',
            'peintre'     => 'Peintre',
            'menuisier'   => 'Menuisier',
            'macon'       => 'Macon',
            'serrurier'   => 'Serrurier',
            'climatiseur' => 'Climatiseur',
            'autre'       => 'Autre',
            default        => $this->category,
        };
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    public function scopeSci(Builder $query, ?int $sciId): Builder
    {
        return $sciId ? $query->where('sci_id', $sciId) : $query;
    }

    public function scopeVisibleByUser(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereIn('sci_id', $user->accessibleSciIds());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
