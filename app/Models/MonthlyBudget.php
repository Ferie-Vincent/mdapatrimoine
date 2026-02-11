<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyBudget extends Model
{
    protected $fillable = [
        'sci_id',
        'month',
        'year',
        'type',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'amount' => 'decimal:2',
        ];
    }

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }
}
