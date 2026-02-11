<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffMember extends Model
{
    protected $fillable = [
        'sci_id',
        'first_name',
        'last_name',
        'role',
        'phone',
        'email',
        'address',
        'base_salary',
        'hire_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'hire_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => $this->last_name . ' ' . $this->first_name);
    }

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
