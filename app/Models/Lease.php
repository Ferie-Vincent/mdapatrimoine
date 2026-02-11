<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lease extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sci_id',
        'property_id',
        'tenant_id',
        'dossier_number',
        'agency_name',
        'entry_inventory_date',
        'start_date',
        'end_date',
        'duration_months',
        'rent_amount',
        'charges_amount',
        'deposit_amount',
        'caution_2_mois',
        'loyers_avances_2_mois',
        'frais_agence',
        'payment_method',
        'due_day',
        'penalty_rate',
        'penalty_delay_days',
        'status',
        'termination_date',
        'termination_reason',
        'signed_lease_path',
        'entry_inspection_path',
        'exit_inspection_path',
        'notice_deposit_date',
        'exit_inventory_date',
        'charges_due_amount',
        'deposit_returned_amount',
        'debts_or_credits_note',
        'actual_exit_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_months' => 'integer',
            'rent_amount' => 'decimal:2',
            'charges_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'caution_2_mois' => 'decimal:2',
            'loyers_avances_2_mois' => 'decimal:2',
            'frais_agence' => 'decimal:2',
            'due_day' => 'integer',
            'penalty_rate' => 'decimal:2',
            'penalty_delay_days' => 'integer',
            'termination_date' => 'date',
            'entry_inventory_date' => 'date',
            'notice_deposit_date' => 'date',
            'exit_inventory_date' => 'date',
            'charges_due_amount' => 'decimal:2',
            'deposit_returned_amount' => 'decimal:2',
            'actual_exit_date' => 'date',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function leaseMonthlies(): HasMany
    {
        return $this->hasMany(LeaseMonthly::class);
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'actif');
    }

    public function scopeForProperty(Builder $query, int $propertyId): Builder
    {
        return $query->where('property_id', $propertyId);
    }
}
