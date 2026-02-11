<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_provider_id',
        'sci_id',
        'title',
        'description',
        'amount',
        'start_date',
        'end_date',
        'status',
        'document_path',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'actif');
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->where('status', 'actif')
            ->whereNotNull('end_date')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays($days));
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                          */
    /* ------------------------------------------------------------------ */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'actif'   => 'Actif',
            'termine' => 'Termine',
            'annule'  => 'Annule',
            default   => $this->status,
        };
    }
}
