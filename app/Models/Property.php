<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sci_id',
        'reference',
        'type',
        'apartment_type_label',
        'floor_label',
        'address',
        'city',
        'description',
        'surface',
        'rooms',
        'status',
        'niveau',
        'numero_porte',
        'nb_keys',
        'nb_clim',
        'cie_meter_number',
        'sodeci_meter_number',
        'photos',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'surface' => 'decimal:2',
            'latitude' => 'float',
            'longitude' => 'float',
            'rooms' => 'integer',
            'nb_keys' => 'integer',
            'nb_clim' => 'integer',
            'photos' => 'array',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    public function scopeSci(Builder $query, int $sciId): Builder
    {
        return $query->where('sci_id', $sciId);
    }

    public function scopeVisibleByUser(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereIn('sci_id', $user->accessibleSciIds());
    }
}
