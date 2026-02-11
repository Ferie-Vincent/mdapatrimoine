<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedCharge extends Model
{
    protected $fillable = [
        'sci_id',
        'month',
        'year',
        'charge_type',
        'label',
        'amount',
        'payment_date',
        'payment_method',
        'receipt_path',
        'signature_data',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    protected function chargeTypeLabel(): Attribute
    {
        return Attribute::get(fn () => match ($this->charge_type) {
            'cie' => 'CIE',
            'sodeci' => 'SODECI',
            'honoraire' => 'Honoraire',
            default => $this->charge_type,
        });
    }
}
