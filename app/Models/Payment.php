<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_monthly_id',
        'sci_id',
        'amount',
        'paid_at',
        'method',
        'reference',
        'note',
        'recorded_by',
        'receipt_path',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function leaseMonthly(): BelongsTo
    {
        return $this->belongsTo(LeaseMonthly::class);
    }

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
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
