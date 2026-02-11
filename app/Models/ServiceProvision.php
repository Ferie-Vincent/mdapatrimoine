<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceProvision extends Model
{
    protected $fillable = [
        'sci_id',
        'month',
        'year',
        'service_type',
        'agent',
        'service_date',
        'amount',
        'status',
        'payment_method',
        'receipt_path',
        'signature_data',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'service_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    public function scopeSci(Builder $query, int $sciId): Builder
    {
        return $query->where('sci_id', $sciId);
    }
}
