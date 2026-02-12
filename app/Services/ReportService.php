<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Get dashboard summary data, optionally filtered by SCI.
     *
     * @return array{
     *     total_expected: float,
     *     total_collected: float,
     *     total_unpaid: float,
     *     recovery_rate: float,
     *     occupied_count: int,
     *     vacant_count: int,
     *     properties_count: int,
     *     active_leases_count: int,
     *     overdue_monthlies: Collection
     * }
     */
    public function dashboardData(?int $sciId = null): array
    {
        $currentMonth = Carbon::now()->format('Y-m');

        // ── Global KPIs (all months) ──
        $allQuery = LeaseMonthly::query();
        if ($sciId !== null) {
            $allQuery->where('sci_id', $sciId);
        }
        $allMonthlies = $allQuery->get();

        $totalExpected  = (float) $allMonthlies->sum('total_due');
        $totalCollected = (float) $allMonthlies->sum('paid_amount');

        // Exclude 'a_venir' from unpaid (these are future, not yet due)
        $upcomingMonthlies = $allMonthlies->where('status', 'a_venir');
        $totalUpcoming  = (float) $upcomingMonthlies->sum('remaining_amount');
        $upcomingCount  = $upcomingMonthlies->count();
        $totalUnpaid    = (float) $allMonthlies->where('status', '!=', 'a_venir')->sum('remaining_amount');

        // Recovery rate based on exigible amounts only (excluding upcoming)
        $exigibleExpected = $totalExpected - $totalUpcoming;
        $recoveryRate   = $exigibleExpected > 0
            ? round(($totalCollected / $exigibleExpected) * 100, 2)
            : 0.0;

        // ── Current month KPIs (exclude a_venir) ──
        $currentMonthlies = $allMonthlies->where('month', $currentMonth);
        $currentExigible = $currentMonthlies->where('status', '!=', 'a_venir');

        $monthExpected  = (float) $currentExigible->sum('total_due');
        $monthCollected = (float) $currentExigible->sum('paid_amount');
        $monthUnpaid    = (float) $currentExigible->sum('remaining_amount');
        $monthRecoveryRate = $monthExpected > 0
            ? round(($monthCollected / $monthExpected) * 100, 2)
            : 0.0;

        // Property counts
        $propertyQuery = Property::query();
        if ($sciId !== null) {
            $propertyQuery->where('sci_id', $sciId);
        }
        $propertiesCount = (clone $propertyQuery)->count();
        $occupiedCount = (clone $propertyQuery)->where('status', 'occupe')->count();
        $vacantCount = (clone $propertyQuery)->where('status', 'disponible')->count();

        // Active leases
        $leaseQuery = Lease::where('status', 'actif');
        if ($sciId !== null) {
            $leaseQuery->where('sci_id', $sciId);
        }
        $activeLeasesCount = $leaseQuery->count();

        // Unpaid monthlies: current month + overdue from past months
        $overdueQuery = LeaseMonthly::whereIn('status', ['impaye', 'partiel', 'en_retard'])
            ->where('remaining_amount', '>', 0)
            ->where('month', '<=', $currentMonth)
            ->with(['lease.tenant', 'lease.property']);

        if ($sciId !== null) {
            $overdueQuery->where('sci_id', $sciId);
        }

        $overdueMonthlies = $overdueQuery->orderBy('due_date', 'asc')->get();

        return [
            'total_expected'      => $totalExpected,
            'total_collected'     => $totalCollected,
            'total_unpaid'        => $totalUnpaid,
            'recovery_rate'       => $recoveryRate,
            'month_expected'      => $monthExpected,
            'month_collected'     => $monthCollected,
            'month_unpaid'        => $monthUnpaid,
            'month_recovery_rate' => $monthRecoveryRate,
            'current_month'       => $currentMonth,
            'occupied_count'      => $occupiedCount,
            'vacant_count'        => $vacantCount,
            'properties_count'    => $propertiesCount,
            'active_leases_count' => $activeLeasesCount,
            'overdue_monthlies'   => $overdueMonthlies,
            'total_upcoming'      => $totalUpcoming,
            'upcoming_count'      => $upcomingCount,
        ];
    }

    /**
     * Get chart data for the dashboard: monthly trends, payment methods, property status, recent activity.
     */
    public function dashboardChartData(?int $sciId = null): array
    {
        $now = Carbon::now();

        // ── Monthly trend (last 12 months) ──
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $month = $date->format('Y-m');

            $query = LeaseMonthly::where('month', $month);
            if ($sciId !== null) {
                $query->where('sci_id', $sciId);
            }
            $monthlies = $query->get();

            $monthlyTrend[] = [
                'month'     => $date->translatedFormat('M Y'),
                'month_key' => $month,
                'expected'  => (float) $monthlies->where('status', '!=', 'a_venir')->sum('total_due'),
                'collected' => (float) $monthlies->sum('paid_amount'),
                'unpaid'    => (float) $monthlies->where('status', '!=', 'a_venir')->sum('remaining_amount'),
            ];
        }

        // ── Payment methods distribution ──
        $paymentQuery = Payment::query();
        if ($sciId !== null) {
            $paymentQuery->where('sci_id', $sciId);
        }
        $paymentMethods = $paymentQuery
            ->selectRaw("method, COUNT(*) as count, SUM(amount) as total")
            ->groupBy('method')
            ->get()
            ->map(fn ($p) => [
                'method' => $p->method,
                'count'  => (int) $p->count,
                'total'  => (float) $p->total,
            ])
            ->toArray();

        // ── Property status distribution ──
        $propQuery = Property::query();
        if ($sciId !== null) {
            $propQuery->where('sci_id', $sciId);
        }
        $propertyStatus = $propQuery
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->get()
            ->map(fn ($p) => [
                'status' => $p->status,
                'count'  => (int) $p->count,
            ])
            ->toArray();

        // ── Recent payments (last 5) ──
        $recentPaymentsQuery = Payment::with(['leaseMonthly.lease.tenant', 'leaseMonthly.lease.property'])
            ->orderByDesc('paid_at')
            ->limit(5);
        if ($sciId !== null) {
            $recentPaymentsQuery->where('sci_id', $sciId);
        }
        $recentPayments = $recentPaymentsQuery->get()->map(fn ($p) => [
            'id'       => $p->id,
            'amount'   => (float) $p->amount,
            'paid_at'  => $p->paid_at?->format('d/m/Y'),
            'method'   => $p->method,
            'tenant'   => $p->leaseMonthly?->lease?->tenant?->full_name ?? '-',
            'property' => $p->leaseMonthly?->lease?->property?->reference ?? '-',
        ])->toArray();

        // ── Recent leases (last 5) ──
        $recentLeasesQuery = Lease::with(['tenant', 'property'])
            ->orderByDesc('created_at')
            ->limit(5);
        if ($sciId !== null) {
            $recentLeasesQuery->where('sci_id', $sciId);
        }
        $recentLeases = $recentLeasesQuery->get()->map(fn ($l) => [
            'id'       => $l->id,
            'tenant'   => $l->tenant?->full_name ?? '-',
            'property' => $l->property?->reference ?? '-',
            'status'   => $l->status,
            'date'     => $l->created_at?->format('d/m/Y'),
        ])->toArray();

        // ── Unpaid rate trend (last 6 months for sparkline) ──
        $unpaidTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $month = $date->format('Y-m');
            $query = LeaseMonthly::where('month', $month);
            if ($sciId !== null) {
                $query->where('sci_id', $sciId);
            }
            $monthlies = $query->where('status', '!=', 'a_venir')->get();
            $totalDue = (float) $monthlies->sum('total_due');
            $remaining = (float) $monthlies->sum('remaining_amount');
            $unpaidTrend[] = $totalDue > 0 ? round(($remaining / $totalDue) * 100, 1) : 0;
        }

        // ── Lease growth trend (last 6 months) ──
        $leaseGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $query = Lease::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);
            if ($sciId !== null) {
                $query->where('sci_id', $sciId);
            }
            $leaseGrowth[] = $query->count();
        }

        // Active leases count
        $activeLeasesQuery = Lease::where('status', 'actif');
        if ($sciId !== null) {
            $activeLeasesQuery->where('sci_id', $sciId);
        }
        $activeLeasesCount = $activeLeasesQuery->count();

        // New leases this month
        $newLeasesQuery = Lease::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year);
        if ($sciId !== null) {
            $newLeasesQuery->where('sci_id', $sciId);
        }
        $newLeasesThisMonth = $newLeasesQuery->count();

        return [
            'monthly_trend'      => $monthlyTrend,
            'payment_methods'    => $paymentMethods,
            'property_status'    => $propertyStatus,
            'recent_payments'    => $recentPayments,
            'recent_leases'      => $recentLeases,
            'unpaid_trend'       => $unpaidTrend,
            'lease_growth'       => $leaseGrowth,
            'active_leases'      => $activeLeasesCount,
            'new_leases_month'   => $newLeasesThisMonth,
        ];
    }

    /**
     * Revenue projection based on active leases.
     */
    public function revenueProjection(?int $sciId = null): array
    {
        $now = Carbon::now();

        // Historical: last 6 months collected
        $historical = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $month = $date->format('Y-m');

            $query = LeaseMonthly::where('month', $month);
            if ($sciId !== null) {
                $query->where('sci_id', $sciId);
            }

            $historical[] = [
                'month' => $date->translatedFormat('M Y'),
                'month_key' => $month,
                'collected' => (float) $query->sum('paid_amount'),
            ];
        }

        // Active leases for projection
        $leaseQuery = Lease::where('status', 'actif');
        if ($sciId !== null) {
            $leaseQuery->where('sci_id', $sciId);
        }
        $activeLeases = $leaseQuery->get(['id', 'rent_amount', 'end_date']);

        // Projected: next 6 months
        $projected = [];
        for ($i = 1; $i <= 6; $i++) {
            $date = $now->copy()->addMonths($i);
            $monthEnd = $date->copy()->endOfMonth();

            $monthProjected = $activeLeases
                ->filter(fn ($l) => $l->end_date === null || $l->end_date->gte($monthEnd))
                ->sum(fn ($l) => (float) $l->rent_amount);

            $projected[] = [
                'month' => $date->translatedFormat('M Y'),
                'month_key' => $date->format('Y-m'),
                'projected' => $monthProjected,
            ];
        }

        return [
            'historical' => $historical,
            'projected' => $projected,
        ];
    }

    /**
     * Detailed monthly report for a specific SCI.
     *
     * Returns a breakdown per property/lease for the given month.
     *
     * @return array{
     *     sci_id: int,
     *     month: string,
     *     total_expected: float,
     *     total_collected: float,
     *     total_remaining: float,
     *     details: Collection
     * }
     */
    public function sciMonthlyReport(int $sciId, string $month): array
    {
        $monthlies = LeaseMonthly::where('sci_id', $sciId)
            ->where('month', $month)
            ->with(['lease.tenant', 'lease.property', 'payments'])
            ->get();

        $totalExpected = (float) $monthlies->sum('total_due');
        $totalCollected = (float) $monthlies->sum('paid_amount');
        $totalRemaining = (float) $monthlies->sum('remaining_amount');

        $details = $monthlies->map(function (LeaseMonthly $monthly) {
            $lease = $monthly->lease;
            return [
                'property_reference' => $lease->property->reference ?? 'N/A',
                'property_address'   => $lease->property->address ?? '',
                'tenant_name'        => $lease->tenant
                    ? "{$lease->tenant->first_name} {$lease->tenant->last_name}"
                    : 'N/A',
                'rent_due'           => (float) $monthly->rent_due,
                'charges_due'        => (float) $monthly->charges_due,
                'penalty_due'        => (float) $monthly->penalty_due,
                'total_due'          => (float) $monthly->total_due,
                'paid_amount'        => (float) $monthly->paid_amount,
                'remaining_amount'   => (float) $monthly->remaining_amount,
                'status'             => $monthly->status,
                'payments'           => $monthly->payments->map(fn ($p) => [
                    'amount'  => (float) $p->amount,
                    'paid_at' => $p->paid_at,
                    'method'  => $p->method,
                ]),
            ];
        });

        return [
            'sci_id'          => $sciId,
            'month'           => $month,
            'total_expected'  => $totalExpected,
            'total_collected' => $totalCollected,
            'total_remaining' => $totalRemaining,
            'details'         => $details,
        ];
    }

    /**
     * Calculate profitability metrics for a specific property.
     *
     * @return array{
     *     property_id: int,
     *     total_rent_collected: float,
     *     total_rent_due: float,
     *     collection_rate: float,
     *     vacancy_rate: float,
     *     total_months: int,
     *     occupied_months: int,
     *     vacant_months: int
     * }
     */
    public function propertyProfitability(int $propertyId): array
    {
        $property = Property::findOrFail($propertyId);

        // Get all leases for this property
        $leases = Lease::where('property_id', $propertyId)->pluck('id');

        $monthlies = LeaseMonthly::whereIn('lease_id', $leases)
            ->orderBy('month')
            ->get();

        $totalRentCollected = (float) $monthlies->sum('paid_amount');
        $totalRentDue = (float) $monthlies->sum('total_due');
        $collectionRate = $totalRentDue > 0
            ? round(($totalRentCollected / $totalRentDue) * 100, 2)
            : 0.0;

        // Calculate occupancy: months with a monthly record = occupied
        $occupiedMonths = $monthlies->pluck('month')->unique()->count();

        // Total months since first monthly or property creation
        $firstMonth = $monthlies->first()?->month;
        if ($firstMonth) {
            $start = Carbon::createFromFormat('Y-m', $firstMonth)->startOfMonth();
            $end = Carbon::now()->startOfMonth();
            $totalMonths = (int) $start->diffInMonths($end) + 1;
        } else {
            $totalMonths = 0;
        }

        $vacantMonths = max(0, $totalMonths - $occupiedMonths);
        $vacancyRate = $totalMonths > 0
            ? round(($vacantMonths / $totalMonths) * 100, 2)
            : 0.0;

        return [
            'property_id'         => $propertyId,
            'total_rent_collected' => $totalRentCollected,
            'total_rent_due'      => $totalRentDue,
            'collection_rate'     => $collectionRate,
            'vacancy_rate'        => $vacancyRate,
            'total_months'        => $totalMonths,
            'occupied_months'     => $occupiedMonths,
            'vacant_months'       => $vacantMonths,
        ];
    }

    /**
     * Get all monthlies with payments for a specific tenant.
     */
    public function tenantPaymentHistory(int $tenantId): Collection
    {
        $leaseIds = Lease::where('tenant_id', $tenantId)->pluck('id');

        return LeaseMonthly::whereIn('lease_id', $leaseIds)
            ->with(['payments', 'lease.property'])
            ->orderBy('month', 'desc')
            ->get();
    }
}
