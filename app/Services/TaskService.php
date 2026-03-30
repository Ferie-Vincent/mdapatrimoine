<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Sci;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function createTask(array $data, int $userId, int $sciId): Task
    {
        $data['user_id'] = $data['user_id'] ?? $userId;
        $data['sci_id'] = $sciId;
        $data['created_by'] = $userId;

        $task = Task::create($data);

        AuditService::log('created', $task, [
            'title' => $task->title,
            'category' => $task->category,
            'scheduled_date' => $task->scheduled_date?->format('Y-m-d'),
        ]);

        return $task;
    }

    public function updateTask(Task $task, array $data): Task
    {
        $oldStatus = $task->status;

        if (isset($data['status']) && $data['status'] === 'fait' && $oldStatus !== 'fait') {
            $data['completed_at'] = now();
        }

        if (isset($data['status']) && $data['status'] !== 'fait' && $oldStatus === 'fait') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        AuditService::log('updated', $task, $data);

        // Generate next occurrence for recurring tasks
        if (isset($data['status']) && $data['status'] === 'fait' && $oldStatus !== 'fait' && $task->recurrence) {
            $this->createNextRecurrence($task);
        }

        return $task->fresh();
    }

    public function deleteTask(Task $task): void
    {
        AuditService::log('deleted', $task, ['title' => $task->title]);

        $task->delete();
    }

    public function moveTask(Task $task, string $newStatus, ?int $newPosition = null): Task
    {
        $oldStatus = $task->status;

        $updates = ['status' => $newStatus];

        if ($newStatus === 'fait' && $oldStatus !== 'fait') {
            $updates['completed_at'] = now();
        }

        if ($newStatus !== 'fait' && $oldStatus === 'fait') {
            $updates['completed_at'] = null;
        }

        if ($newPosition !== null) {
            $updates['position'] = $newPosition;
        }

        $task->update($updates);

        AuditService::log('updated', $task, [
            'status' => "{$oldStatus} → {$newStatus}",
        ]);

        // Generate next occurrence for recurring tasks
        if ($newStatus === 'fait' && $oldStatus !== 'fait' && $task->recurrence) {
            $this->createNextRecurrence($task);
        }

        return $task->fresh();
    }

    public function reorderTasks(array $taskIds): void
    {
        DB::transaction(function () use ($taskIds) {
            foreach ($taskIds as $position => $taskId) {
                Task::where('id', $taskId)->update(['position' => $position]);
            }
        });
    }

    public function getKanbanData(int $userId, ?int $sciId, ?string $date = null, bool $showArchived = false): array
    {
        $date = $date ?? today()->format('Y-m-d');

        $user = User::findOrFail($userId);

        $query = Task::query()
            ->with(['user:id,name', 'sci:id,name', 'related'])
            ->visibleByUser($user)
            ->forDate($date);

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        $tasks = $query->orderBy('position')->get();

        $columns = [
            'a_faire' => [],
            'en_cours' => [],
            'bloque' => [],
            'fait' => [],
            'archive' => [],
        ];

        foreach ($tasks as $task) {
            if ($task->status === 'archive' && !$showArchived) {
                continue;
            }
            $columns[$task->status][] = $task;
        }

        return $columns;
    }

    public function assignTask(Task $task, int $targetUserId): Task
    {
        $oldUserId = $task->user_id;

        $task->update(['user_id' => $targetUserId]);

        AuditService::log('updated', $task, [
            'assigned_from' => $oldUserId,
            'assigned_to' => $targetUserId,
        ]);

        return $task->fresh();
    }

    public function generateAutoTasks(?int $sciId = null): int
    {
        $count = 0;

        $scis = $sciId
            ? Sci::where('id', $sciId)->where('is_active', true)->get()
            : Sci::where('is_active', true)->get();

        foreach ($scis as $sci) {
            $count += $this->generatePaymentTasks($sci->id);
            $count += $this->generateReminderTasks($sci->id);
            $count += $this->generateLeaseExpiryTasks($sci->id);
            $count += $this->generateIdExpiryTasks($sci->id);
        }

        return $count;
    }

    public function archiveOldTasks(int $daysOld = 7): int
    {
        return Task::where('status', 'fait')
            ->where('completed_at', '<=', Carbon::now()->subDays($daysOld))
            ->update(['status' => 'archive']);
    }

    /* ------------------------------------------------------------------ */
    /*  Recurrence                                                          */
    /* ------------------------------------------------------------------ */

    private function createNextRecurrence(Task $task): ?Task
    {
        $nextDate = match ($task->recurrence) {
            'quotidien' => $task->scheduled_date->addDay(),
            'hebdomadaire' => $task->scheduled_date->addWeek(),
            'mensuel' => $task->scheduled_date->addMonth(),
            'trimestriel' => $task->scheduled_date->addMonths(3),
            'annuel' => $task->scheduled_date->addYear(),
            default => null,
        };

        if (!$nextDate) {
            return null;
        }

        if ($task->recurrence_end_date && $nextDate->gt($task->recurrence_end_date)) {
            return null;
        }

        $nextTask = Task::create([
            'sci_id' => $task->sci_id,
            'user_id' => $task->user_id,
            'created_by' => $task->created_by,
            'title' => $task->title,
            'description' => $task->description,
            'category' => $task->category,
            'related_type' => $task->related_type,
            'related_id' => $task->related_id,
            'amount' => $task->amount,
            'priority' => $task->priority,
            'status' => 'a_faire',
            'scheduled_date' => $nextDate,
            'scheduled_time' => $task->scheduled_time,
            'is_auto' => false,
            'recurrence' => $task->recurrence,
            'recurrence_end_date' => $task->recurrence_end_date,
            'position' => 0,
        ]);

        AuditService::log('created', $nextTask, [
            'recurrence_from' => $task->id,
            'recurrence_type' => $task->recurrence,
        ]);

        return $nextTask;
    }

    /* ------------------------------------------------------------------ */
    /*  Private auto-generation helpers                                     */
    /* ------------------------------------------------------------------ */

    private function generatePaymentTasks(int $sciId): int
    {
        $count = 0;
        $assignee = $this->getAssigneeForSci($sciId);
        if (!$assignee) {
            return 0;
        }

        $unpaidMonthlies = LeaseMonthly::where('sci_id', $sciId)
            ->unpaid()
            ->where('remaining_amount', '>', 0)
            ->with(['lease.tenant', 'lease.property'])
            ->get();

        foreach ($unpaidMonthlies as $monthly) {
            if ($this->isDuplicate(LeaseMonthly::class, $monthly->id, today()->format('Y-m-d'))) {
                continue;
            }

            $tenant = $monthly->lease->tenant ?? null;
            $property = $monthly->lease->property ?? null;
            $tenantName = $tenant?->full_name ?? 'Locataire';
            $propertyRef = $property?->reference ?? '';

            Task::create([
                'sci_id' => $sciId,
                'user_id' => $assignee->id,
                'created_by' => null,
                'title' => "Encaisser loyer {$monthly->month} — {$tenantName} ({$propertyRef})",
                'category' => 'encaissement',
                'related_type' => LeaseMonthly::class,
                'related_id' => $monthly->id,
                'amount' => $monthly->remaining_amount,
                'priority' => $monthly->status === 'en_retard' ? 'haute' : 'moyenne',
                'status' => 'a_faire',
                'scheduled_date' => today(),
                'is_auto' => true,
                'position' => 0,
            ]);

            $count++;
        }

        return $count;
    }

    private function generateReminderTasks(int $sciId): int
    {
        $count = 0;
        $assignee = $this->getAssigneeForSci($sciId);
        if (!$assignee) {
            return 0;
        }

        $overdueMonthlies = LeaseMonthly::where('sci_id', $sciId)
            ->whereIn('status', ['impaye', 'partiel', 'en_retard'])
            ->where('remaining_amount', '>', 0)
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now()->subDays(5))
            ->with(['lease.tenant', 'lease.property'])
            ->get();

        foreach ($overdueMonthlies as $monthly) {
            if ($this->isDuplicate(LeaseMonthly::class, $monthly->id, today()->format('Y-m-d'), 'relance')) {
                continue;
            }

            // Skip if a reminder was already sent recently (last 7 days)
            $recentReminder = $monthly->reminders()
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->exists();

            if ($recentReminder) {
                continue;
            }

            $tenant = $monthly->lease->tenant ?? null;
            $tenantName = $tenant?->full_name ?? 'Locataire';

            Task::create([
                'sci_id' => $sciId,
                'user_id' => $assignee->id,
                'created_by' => null,
                'title' => "Relancer {$tenantName} — impayé {$monthly->month}",
                'category' => 'relance',
                'related_type' => LeaseMonthly::class,
                'related_id' => $monthly->id,
                'amount' => $monthly->remaining_amount,
                'priority' => 'haute',
                'status' => 'a_faire',
                'scheduled_date' => today(),
                'is_auto' => true,
                'position' => 0,
            ]);

            $count++;
        }

        return $count;
    }

    private function generateLeaseExpiryTasks(int $sciId): int
    {
        $count = 0;
        $assignee = $this->getAssigneeForSci($sciId);
        if (!$assignee) {
            return 0;
        }

        $expiringLeases = Lease::where('sci_id', $sciId)
            ->where('status', 'actif')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', Carbon::now()->addDays(30))
            ->with(['tenant', 'property'])
            ->get();

        foreach ($expiringLeases as $lease) {
            if ($this->isDuplicate(Lease::class, $lease->id, today()->format('Y-m-d'))) {
                continue;
            }

            $tenant = $lease->tenant;
            $endDate = $lease->end_date->format('d/m/Y');

            Task::create([
                'sci_id' => $sciId,
                'user_id' => $assignee->id,
                'created_by' => null,
                'title' => "Bail #{$lease->id} expire le {$endDate}" . ($tenant ? " — {$tenant->full_name}" : ''),
                'category' => 'administratif',
                'related_type' => Lease::class,
                'related_id' => $lease->id,
                'priority' => 'haute',
                'status' => 'a_faire',
                'scheduled_date' => today(),
                'is_auto' => true,
                'position' => 0,
            ]);

            $count++;
        }

        return $count;
    }

    private function generateIdExpiryTasks(int $sciId): int
    {
        $count = 0;
        $assignee = $this->getAssigneeForSci($sciId);
        if (!$assignee) {
            return 0;
        }

        $expiringTenants = Tenant::where('sci_id', $sciId)
            ->where('is_active', true)
            ->whereNotNull('id_expiration')
            ->where('id_expiration', '<=', Carbon::now()->addDays(30))
            ->get();

        foreach ($expiringTenants as $tenant) {
            if ($this->isDuplicate(Tenant::class, $tenant->id, today()->format('Y-m-d'))) {
                continue;
            }

            $expirationDate = $tenant->id_expiration->format('d/m/Y');

            Task::create([
                'sci_id' => $sciId,
                'user_id' => $assignee->id,
                'created_by' => null,
                'title' => "Pièce ID de {$tenant->full_name} expire le {$expirationDate}",
                'category' => 'administratif',
                'related_type' => Tenant::class,
                'related_id' => $tenant->id,
                'priority' => 'moyenne',
                'status' => 'a_faire',
                'scheduled_date' => today(),
                'is_auto' => true,
                'position' => 0,
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Check if an auto-task already exists for this entity+date (any status, including archived).
     */
    private function isDuplicate(string $relatedType, int $relatedId, string $scheduledDate, ?string $category = null): bool
    {
        $query = Task::where('related_type', $relatedType)
            ->where('related_id', $relatedId)
            ->whereDate('scheduled_date', $scheduledDate);

        if ($category) {
            $query->where('category', $category);
        }

        return $query->exists();
    }

    /**
     * Get the first active gestionnaire or super_admin for a SCI.
     * Never assigns to lecture_seule users.
     */
    private function getAssigneeForSci(int $sciId): ?User
    {
        // First try gestionnaires assigned to this SCI
        $gestionnaire = User::where('is_active', true)
            ->where('role', 'gestionnaire')
            ->whereHas('scis', fn ($q) => $q->where('scis.id', $sciId))
            ->first();

        if ($gestionnaire) {
            return $gestionnaire;
        }

        // Fallback to any super_admin
        return User::where('is_active', true)
            ->where('role', 'super_admin')
            ->first();
    }
}
