<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $sciId = $request->attributes->get('sci_id');

        $query = AuditLog::query()->with('user');

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        if ($entityType = $request->input('entity_type')) {
            $query->where('entity_type', $entityType);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $auditLogs = $query->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $users = \App\Models\User::orderBy('name')->pluck('name', 'id');

        return view('audit-logs.index', compact('auditLogs', 'users'));
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user');

        return view('audit-logs.show', compact('auditLog'));
    }
}
