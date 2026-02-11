<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function scis(): BelongsToMany
    {
        return $this->belongsToMany(Sci::class, 'sci_user');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Role helpers                                                        */
    /* ------------------------------------------------------------------ */

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }

    public function isLectureSeule(): bool
    {
        return $this->role === 'lecture_seule';
    }

    /**
     * Return the list of SCI ids the user has access to.
     * Super-admins have access to every SCI.
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    public function accessibleSciIds(): Collection
    {
        if ($this->isSuperAdmin()) {
            return Sci::pluck('id');
        }

        return $this->scis()->pluck('scis.id');
    }
}
