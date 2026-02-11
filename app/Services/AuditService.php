<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * Log an audit entry.
     *
     * Accepts two calling conventions:
     *   log('action', $model, $changes)           — entity type/id/sci_id extracted from model
     *   log('action', 'ClassName', $id, $changes, $sciId) — explicit primitives
     */
    public static function log(
        string $action,
        Model|string|null $entityTypeOrModel = null,
        array|int|null $entityIdOrChanges = null,
        ?array $changes = null,
        ?int $sciId = null
    ): AuditLog {
        if ($entityTypeOrModel instanceof Model) {
            $model = $entityTypeOrModel;
            $entityType = $model->getMorphClass();
            $entityId = $model->getKey();
            $resolvedChanges = is_array($entityIdOrChanges) ? $entityIdOrChanges : $changes;
            $resolvedSciId = $sciId ?? (isset($model->sci_id) ? (int) $model->sci_id : null);
        } else {
            $entityType = $entityTypeOrModel;
            $entityId = is_int($entityIdOrChanges) ? $entityIdOrChanges : null;
            $resolvedChanges = $changes;
            $resolvedSciId = $sciId;
        }

        return AuditLog::create([
            'user_id'     => auth()->id(),
            'sci_id'      => $resolvedSciId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'changes'     => $resolvedChanges,
            'ip_address'  => request()?->ip(),
            'user_agent'  => request()?->userAgent(),
        ]);
    }
}
