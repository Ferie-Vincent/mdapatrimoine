<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sci_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_secondary',
        'whatsapp_phone',
        'address',
        'id_type',
        'id_number',
        'id_expiration',
        'id_file_path',
        'id_file_verso_path',
        'payment_receipt_path',
        'profession',
        'employer',
        'emergency_contact_name',
        'emergency_contact_phone',
        'guarantor_name',
        'guarantor_phone',
        'guarantor_address',
        'guarantor_id_number',
        'guarantor_profession',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'id_expiration' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                          */
    /* ------------------------------------------------------------------ */

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
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
