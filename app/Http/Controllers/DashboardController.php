<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\ProviderContract;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {}

    /**
     * Display the main dashboard with stats, charts, and overdue monthlies.
     */
    public function index(Request $request): View
    {
        $sciId = $request->attributes->get('sci_id');

        $data = $this->reportService->dashboardData($sciId);
        $chartData = $this->reportService->dashboardChartData($sciId);

        $propertiesQuery = Property::whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($sciId) {
            $propertiesQuery->where('sci_id', $sciId);
        }

        $mapProperties = $propertiesQuery->get(['id', 'reference', 'address', 'type', 'status', 'latitude', 'longitude']);

        $projection = $this->reportService->revenueProjection($sciId);

        $expiringContractsCount = ProviderContract::where('status', 'actif')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addDays(30))
            ->where('end_date', '>=', now())
            ->when($sciId, fn ($q) => $q->where('sci_id', $sciId))
            ->count();

        return view('dashboard.index', [
            'mapProperties' => $mapProperties,
            'projection' => $projection,
            'expiringContractsCount' => $expiringContractsCount,
            'stats' => [
                'total_expected'     => $data['total_expected'],
                'total_collected'    => $data['total_collected'],
                'total_unpaid'       => $data['total_unpaid'],
                'recovery_rate'      => $data['recovery_rate'],
                'occupied_count'     => $data['occupied_count'],
                'vacant_count'       => $data['vacant_count'],
                'properties_count'   => $data['properties_count'],
                'active_leases_count'=> $data['active_leases_count'],
            ],
            'monthStats' => [
                'expected'      => $data['month_expected'],
                'collected'     => $data['month_collected'],
                'unpaid'        => $data['month_unpaid'],
                'recovery_rate' => $data['month_recovery_rate'],
                'label'         => $data['current_month'],
            ],
            'overdueMonthlies' => $data['overdue_monthlies'],
            'chartData'        => $chartData,
        ]);
    }

    /**
     * Switch the active SCI in the session.
     */
    public function switchSci(Request $request): RedirectResponse
    {
        $request->validate([
            'sci_id' => ['nullable', 'exists:scis,id'],
        ]);

        $sciId = $request->input('sci_id');

        if (empty($sciId) && $request->user()->isSuperAdmin()) {
            $request->session()->forget('sci_id');
        } else {
            session(['sci_id' => (int) $sciId]);
        }

        return redirect()->back()->with('success', 'SCI active modifiée avec succès.');
    }
}
