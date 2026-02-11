<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sci extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'rccm',
        'ifu',
        'address',
        'phone',
        'email',
        'bank_name',
        'bank_iban',
        'logo_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sci_user');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function leaseMonthlies(): HasMany
    {
        return $this->hasMany(LeaseMonthly::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function serviceProviders(): HasMany
    {
        return $this->hasMany(ServiceProvider::class);
    }

    public function providerContracts(): HasMany
    {
        return $this->hasMany(ProviderContract::class);
    }
}
