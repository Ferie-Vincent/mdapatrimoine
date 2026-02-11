<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LeaseMonthly;
use App\Services\MonthlyGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaseMonthlyController extends Controller
{
    public function __construct(
        private readonly MonthlyGenerationService $monthlyGenerationService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', LeaseMonthly::class);

        $sciId = $request->attributes->get('sci_id');

        $query = LeaseMonthly::query()->with(['lease.property', 'lease.tenant']);

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($leaseId = $request->input('lease_id')) {
            $query->where('lease_id', $leaseId);
        }

        if ($month = $request->input('month')) {
            $query->where('month', $month);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('lease.tenant', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $monthlies = $query->orderByDesc('month')
            ->paginate(15)
            ->withQueryString();

        return view('monthlies.index', compact('monthlies'));
    }

    public function show(LeaseMonthly $monthly): View
    {
        $this->authorize('view', $monthly);

        $monthly->load(['lease.property', 'lease.tenant', 'payments.recorder']);

        return view('monthlies.show', compact('monthly'));
    }

    public function generateMonthlies(Request $request): RedirectResponse
    {
        $this->authorize('create', LeaseMonthly::class);

        $count = $this->monthlyGenerationService->generateAllPending();

        return redirect()
            ->back()
            ->with('success', "{$count} échéance(s) mensuelle(s) générée(s) avec succès.");
    }
}
