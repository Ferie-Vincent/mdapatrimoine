<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'sci_id',
        'user_id',
        'created_by',
        'title',
        'description',
        'category',
        'related_type',
        'related_id',
        'amount',
        'priority',
        'status',
        'scheduled_date',
        'scheduled_time',
        'reminder_at',
        'reminder_sent',
        'is_auto',
        'recurrence',
        'recurrence_end_date',
        'completed_at',
        'position',
    ];

    protected $appends = ['is_overdue', 'is_recurring'];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'reminder_at' => 'datetime',
            'completed_at' => 'datetime',
            'reminder_sent' => 'boolean',
            'is_auto' => 'boolean',
            'position' => 'integer',
            'amount' => 'decimal:2',
            'recurrence_end_date' => 'date',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                          */
    /* ------------------------------------------------------------------ */

    public function getIsOverdueAttribute(): bool
    {
        return $this->scheduled_date !== null
            && $this->scheduled_date < today()
            && in_array($this->status, ['a_faire', 'en_cours']);
    }

    public function getIsRecurringAttribute(): bool
    {
        return $this->recurrence !== null;
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    public function sci(): BelongsTo
    {
        return $this->belongsTo(Sci::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function related(): MorphTo
    {
        return $this->morphTo('related', 'related_type', 'related_id');
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

        return $query->where('user_id', $user->id);
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('scheduled_date', $date);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['a_faire', 'en_cours', 'bloque']);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'fait');
    }

    public function scopeNotArchived(Builder $query): Builder
    {
        return $query->where('status', '!=', 'archive');
    }
}
