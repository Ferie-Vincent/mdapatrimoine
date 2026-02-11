<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreReminderRequest;
use App\Models\LeaseMonthly;
use App\Models\Reminder;
use App\Services\ReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderController extends Controller
{
    public function __construct(
        private readonly ReminderService $reminderService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Reminder::class);

        $sciId = $request->attributes->get('sci_id');

        // Remove brouillon reminders where the monthly has been fully paid
        Reminder::where('status', 'brouillon')
            ->whereHas('leaseMonthly', fn ($q) => $q->where('remaining_amount', '<=', 0))
            ->delete();

        $query = Reminder::query()
            ->with(['leaseMonthly.lease.tenant', 'leaseMonthly.lease.property', 'sender']);

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($channel = $request->input('channel')) {
            $query->where('channel', $channel);
        }

        if ($level = $request->input('level')) {
            $query->where('level', (int) $level);
        }

        $reminders = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $unpaidMonthlies = LeaseMonthly::whereIn('status', ['impaye', 'partiel', 'en_retard'])
            ->where('remaining_amount', '>', 0)
            ->when($sciId, fn ($q) => $q->where('sci_id', $sciId))
            ->with(['lease.tenant', 'lease.property'])
            ->orderByDesc('month')
            ->get();

        $baseQuery = Reminder::query()->when($sciId, fn ($q) => $q->where('sci_id', $sciId));
        $countBrouillon = (clone $baseQuery)->where('status', 'brouillon')->count();
        $countEnvoyeAujourdhui = (clone $baseQuery)->where('status', 'envoye')->whereDate('sent_at', today())->count();
        $countEchec = (clone $baseQuery)->where('status', 'echec')->count();

        return view('reminders.index', compact('reminders', 'unpaidMonthlies', 'countBrouillon', 'countEnvoyeAujourdhui', 'countEchec'));
    }

    public function store(StoreReminderRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Reminder::class);

        $data = $request->validated();

        $monthly = LeaseMonthly::findOrFail($data['lease_monthly_id']);
        $level = (int) ($data['level'] ?? 1);

        // Auto-generate message from settings template if not provided
        $message = $data['message'] ?? null;
        if (empty($message)) {
            $monthly->loadMissing(['lease.tenant', 'lease.property']);
            $tenant = $monthly->lease->tenant ?? null;
            $property = $monthly->lease->property ?? null;

            $message = $this->reminderService->buildMessage(
                $level,
                $tenant->full_name ?? 'Locataire',
                $property->reference ?? 'N/A',
                $monthly->month,
                number_format((float) $monthly->remaining_amount, 0, ',', ' ')
            );
        }

        $reminder = $this->reminderService->createReminder(
            $monthly,
            $data['channel'],
            $message,
            $level
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Relance creee avec succes.']);
        }

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Relance creee avec succes.');
    }

    public function send(Request $request, Reminder $reminder): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $reminder);

        $success = $this->reminderService->sendReminder($reminder);

        $message = $success
            ? 'Relance envoyee avec succes.'
            : 'Echec de l\'envoi: ' . ($reminder->fresh()->error_message ?? 'erreur inconnue');

        if ($request->expectsJson()) {
            return response()->json(['success' => $success, 'message' => $message]);
        }

        return redirect()
            ->back()
            ->with($success ? 'success' : 'error', $message);
    }

    public function autoGenerate(Request $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Reminder::class);

        $count = $this->reminderService->autoGenerateReminders();

        $message = "{$count} relance(s) generee(s) automatiquement.";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'count' => $count]);
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }
}
