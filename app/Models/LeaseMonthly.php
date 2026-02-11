<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaseMonthly extends Model
{
    use HasFactory;

    protected $table = 'lease_monthlies';

    protected $fillable = [
        'lease_id',
        'sci_id',
        'month',
        'rent_due',
        'charges_due',
        'penalty_due',
        'total_due',
        'paid_amount',
        'remaining_amount',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'rent_due' => 'decimal:2',
            'charges_due' => 'decimal:2',
            'penalty_due' => 'decimal:2',
            'total_due' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
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

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('status', ['impaye', 'partiel', 'en_retard']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'en_retard');
    }
}
