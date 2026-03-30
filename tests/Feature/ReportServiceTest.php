<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Sci;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = app()->make(ReportService::class);
    }

    /**
     * Create a complete hierarchy: User > SCI > Property > Tenant > Lease > LeaseMonthly.
     */
    private function createFullSetup(?array $overrides = []): array
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($user);

        $sci = Sci::factory()->create();
        $sci->users()->attach($user);

        $property = Property::factory()->create([
            'sci_id' => $sci->id,
            'status' => $overrides['property_status'] ?? 'occupe',
        ]);

        $tenant = Tenant::factory()->create(['sci_id' => $sci->id]);

        $lease = Lease::factory()->create([
            'sci_id'      => $sci->id,
            'property_id' => $property->id,
            'tenant_id'   => $tenant->id,
            'status'      => 'actif',
            'rent_amount' => $overrides['rent_amount'] ?? 150000,
        ]);

        return compact('user', 'sci', 'property', 'tenant', 'lease');
    }

    private function createMonthly(Lease $lease, string $month, array $overrides = []): LeaseMonthly
    {
        return LeaseMonthly::factory()->create(array_merge([
            'lease_id'         => $lease->id,
            'sci_id'           => $lease->sci_id,
            'month'            => $month,
            'rent_due'         => 150000,
            'charges_due'      => 0,
            'penalty_due'      => 0,
            'total_due'        => 150000,
            'paid_amount'      => 0,
            'remaining_amount' => 150000,
            'status'           => 'impaye',
            'due_date'         => Carbon::createFromFormat('Y-m', $month)->startOfMonth()->addDays(4),
        ], $overrides));
    }

    /* ------------------------------------------------------------------ */
    /*  dashboardData()                                                     */
    /* ------------------------------------------------------------------ */

    public function test_dashboard_data_returns_correct_structure(): void
    {
        $data = $this->reportService->dashboardData();

        $this->assertArrayHasKey('total_expected', $data);
        $this->assertArrayHasKey('total_collected', $data);
        $this->assertArrayHasKey('total_unpaid', $data);
        $this->assertArrayHasKey('recovery_rate', $data);
        $this->assertArrayHasKey('month_expected', $data);
        $this->assertArrayHasKey('month_collected', $data);
        $this->assertArrayHasKey('properties_count', $data);
        $this->assertArrayHasKey('active_leases_count', $data);
        $this->assertArrayHasKey('overdue_monthlies', $data);
    }

    public function test_dashboard_data_empty_database_returns_zeros(): void
    {
        $data = $this->reportService->dashboardData();

        $this->assertEquals(0.0, $data['total_expected']);
        $this->assertEquals(0.0, $data['total_collected']);
        $this->assertEquals(0.0, $data['total_unpaid']);
        $this->assertEquals(0.0, $data['recovery_rate']);
        $this->assertEquals(0, $data['properties_count']);
        $this->assertEquals(0, $data['active_leases_count']);
    }

    public function test_dashboard_data_calculates_totals_correctly(): void
    {
        $setup = $this->createFullSetup();
        $currentMonth = Carbon::now()->format('Y-m');

        $this->createMonthly($setup['lease'], $currentMonth, [
            'total_due'        => 150000,
            'paid_amount'      => 100000,
            'remaining_amount' => 50000,
            'status'           => 'partiel',
        ]);

        $data = $this->reportService->dashboardData($setup['sci']->id);

        $this->assertEquals(150000, $data['total_expected']);
        $this->assertEquals(100000, $data['total_collected']);
        $this->assertEquals(50000, $data['total_unpaid']);
    }

    public function test_dashboard_data_excludes_a_venir_from_unpaid(): void
    {
        $setup = $this->createFullSetup();
        $currentMonth = Carbon::now()->format('Y-m');
        $futureMonth = Carbon::now()->addMonth()->format('Y-m');

        // Current month: impayé
        $this->createMonthly($setup['lease'], $currentMonth, [
            'total_due'        => 150000,
            'paid_amount'      => 0,
            'remaining_amount' => 150000,
            'status'           => 'impaye',
        ]);

        // Future month: à venir (not yet due)
        $this->createMonthly($setup['lease'], $futureMonth, [
            'total_due'        => 150000,
            'paid_amount'      => 0,
            'remaining_amount' => 150000,
            'status'           => 'a_venir',
        ]);

        $data = $this->reportService->dashboardData($setup['sci']->id);

        // Only current month counts as unpaid, not the future one
        $this->assertEquals(150000, $data['total_unpaid']);
        $this->assertEquals(150000, $data['total_upcoming']);
        $this->assertEquals(1, $data['upcoming_count']);
    }

    public function test_dashboard_data_recovery_rate_calculation(): void
    {
        $setup = $this->createFullSetup();
        $currentMonth = Carbon::now()->format('Y-m');

        $this->createMonthly($setup['lease'], $currentMonth, [
            'total_due'        => 200000,
            'paid_amount'      => 150000,
            'remaining_amount' => 50000,
            'status'           => 'partiel',
        ]);

        $data = $this->reportService->dashboardData($setup['sci']->id);

        // 150000 / 200000 = 75%
        $this->assertEquals(75.0, $data['recovery_rate']);
    }

    public function test_dashboard_data_filters_by_sci(): void
    {
        $setup1 = $this->createFullSetup();
        $currentMonth = Carbon::now()->format('Y-m');

        $this->createMonthly($setup1['lease'], $currentMonth, [
            'total_due' => 150000, 'paid_amount' => 150000, 'remaining_amount' => 0, 'status' => 'paye',
        ]);

        // Second SCI with different data
        $sci2 = Sci::factory()->create();
        $property2 = Property::factory()->create(['sci_id' => $sci2->id, 'status' => 'occupe']);
        $tenant2 = Tenant::factory()->create(['sci_id' => $sci2->id]);
        $lease2 = Lease::factory()->create([
            'sci_id' => $sci2->id, 'property_id' => $property2->id, 'tenant_id' => $tenant2->id, 'status' => 'actif',
        ]);
        $this->createMonthly($lease2, $currentMonth, [
            'sci_id' => $sci2->id, 'total_due' => 300000, 'paid_amount' => 0, 'remaining_amount' => 300000, 'status' => 'impaye',
        ]);

        // Filter by SCI 1 — should only see its data
        $data = $this->reportService->dashboardData($setup1['sci']->id);
        $this->assertEquals(150000, $data['total_collected']);
        $this->assertEquals(1, $data['properties_count']);

        // Filter by SCI 2
        $data2 = $this->reportService->dashboardData($sci2->id);
        $this->assertEquals(0.0, $data2['total_collected']);
        $this->assertEquals(300000, $data2['total_unpaid']);
    }

    public function test_dashboard_data_property_counts(): void
    {
        $setup = $this->createFullSetup(['property_status' => 'occupe']);
        Property::factory()->create(['sci_id' => $setup['sci']->id, 'status' => 'disponible']);
        Property::factory()->create(['sci_id' => $setup['sci']->id, 'status' => 'disponible']);

        $data = $this->reportService->dashboardData($setup['sci']->id);

        $this->assertEquals(3, $data['properties_count']);
        $this->assertEquals(1, $data['occupied_count']);
        $this->assertEquals(2, $data['vacant_count']);
    }

    public function test_dashboard_data_overdue_monthlies(): void
    {
        $setup = $this->createFullSetup();
        $pastMonth = Carbon::now()->subMonth()->format('Y-m');

        $this->createMonthly($setup['lease'], $pastMonth, [
            'total_due' => 150000, 'paid_amount' => 0, 'remaining_amount' => 150000, 'status' => 'en_retard',
        ]);

        $data = $this->reportService->dashboardData($setup['sci']->id);

        $this->assertCount(1, $data['overdue_monthlies']);
        $this->assertEquals('en_retard', $data['overdue_monthlies']->first()->status);
    }

    /* ------------------------------------------------------------------ */
    /*  dashboardChartData()                                                */
    /* ------------------------------------------------------------------ */

    public function test_chart_data_returns_correct_structure(): void
    {
        $data = $this->reportService->dashboardChartData();

        $this->assertArrayHasKey('monthly_trend', $data);
        $this->assertArrayHasKey('payment_methods', $data);
        $this->assertArrayHasKey('property_status', $data);
        $this->assertArrayHasKey('recent_payments', $data);
        $this->assertArrayHasKey('recent_leases', $data);
        $this->assertArrayHasKey('unpaid_trend', $data);
        $this->assertArrayHasKey('lease_growth', $data);
        $this->assertCount(12, $data['monthly_trend']);
        $this->assertCount(6, $data['unpaid_trend']);
        $this->assertCount(6, $data['lease_growth']);
    }

    public function test_chart_data_monthly_trend_reflects_data(): void
    {
        $setup = $this->createFullSetup();
        $currentMonth = Carbon::now()->format('Y-m');

        $this->createMonthly($setup['lease'], $currentMonth, [
            'total_due' => 150000, 'paid_amount' => 100000, 'remaining_amount' => 50000, 'status' => 'partiel',
        ]);

        $data = $this->reportService->dashboardChartData($setup['sci']->id);

        $lastEntry = end($data['monthly_trend']);
        $this->assertEquals(150000, $lastEntry['expected']);
        $this->assertEquals(100000, $lastEntry['collected']);
        $this->assertEquals(50000, $lastEntry['unpaid']);
    }

    public function test_chart_data_payment_methods_distribution(): void
    {
        $setup = $this->createFullSetup();
        $currentMonth = Carbon::now()->format('Y-m');
        $monthly = $this->createMonthly($setup['lease'], $currentMonth);

        Payment::factory()->create([
            'lease_monthly_id' => $monthly->id, 'sci_id' => $setup['sci']->id,
            'amount' => 50000, 'method' => 'especes',
        ]);
        Payment::factory()->create([
            'lease_monthly_id' => $monthly->id, 'sci_id' => $setup['sci']->id,
            'amount' => 80000, 'method' => 'mobile_money',
        ]);

        $data = $this->reportService->dashboardChartData($setup['sci']->id);

        $this->assertCount(2, $data['payment_methods']);
    }

    /* ------------------------------------------------------------------ */
    /*  sciMonthlyReport()                                                  */
    /* ------------------------------------------------------------------ */

    public function test_sci_monthly_report_returns_correct_data(): void
    {
        $setup = $this->createFullSetup();
        $currentMonth = Carbon::now()->format('Y-m');

        $monthly = $this->createMonthly($setup['lease'], $currentMonth, [
            'rent_due' => 150000, 'charges_due' => 20000, 'total_due' => 170000,
            'paid_amount' => 100000, 'remaining_amount' => 70000, 'status' => 'partiel',
        ]);

        Payment::factory()->create([
            'lease_monthly_id' => $monthly->id, 'sci_id' => $setup['sci']->id, 'amount' => 100000,
        ]);

        $report = $this->reportService->sciMonthlyReport($setup['sci']->id, $currentMonth);

        $this->assertEquals($setup['sci']->id, $report['sci_id']);
        $this->assertEquals($currentMonth, $report['month']);
        $this->assertEquals(170000, $report['total_expected']);
        $this->assertEquals(100000, $report['total_collected']);
        $this->assertEquals(70000, $report['total_remaining']);
        $this->assertCount(1, $report['details']);

        $detail = $report['details']->first();
        $this->assertEquals($setup['property']->reference, $detail['property_reference']);
        $this->assertEquals(150000, $detail['rent_due']);
        $this->assertEquals(20000, $detail['charges_due']);
        $this->assertCount(1, $detail['payments']);
    }

    /* ------------------------------------------------------------------ */
    /*  propertyProfitability()                                             */
    /* ------------------------------------------------------------------ */

    public function test_property_profitability_calculation(): void
    {
        $setup = $this->createFullSetup();
        $lastMonth = Carbon::now()->subMonth()->format('Y-m');
        $currentMonth = Carbon::now()->format('Y-m');

        $this->createMonthly($setup['lease'], $lastMonth, [
            'total_due' => 150000, 'paid_amount' => 150000, 'remaining_amount' => 0, 'status' => 'paye',
        ]);
        $this->createMonthly($setup['lease'], $currentMonth, [
            'total_due' => 150000, 'paid_amount' => 0, 'remaining_amount' => 150000, 'status' => 'impaye',
        ]);

        $result = $this->reportService->propertyProfitability($setup['property']->id);

        $this->assertEquals($setup['property']->id, $result['property_id']);
        $this->assertEquals(300000, $result['total_rent_due']);
        $this->assertEquals(150000, $result['total_rent_collected']);
        $this->assertEquals(50.0, $result['collection_rate']);
        $this->assertEquals(2, $result['occupied_months']);
    }

    public function test_property_profitability_no_leases(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($user);
        $sci = Sci::factory()->create();
        $property = Property::factory()->create(['sci_id' => $sci->id, 'status' => 'disponible']);

        $result = $this->reportService->propertyProfitability($property->id);

        $this->assertEquals(0.0, $result['collection_rate']);
        $this->assertEquals(0.0, $result['vacancy_rate']);
        $this->assertEquals(0, $result['total_months']);
    }

    /* ------------------------------------------------------------------ */
    /*  tenantPaymentHistory()                                              */
    /* ------------------------------------------------------------------ */

    public function test_tenant_payment_history(): void
    {
        $setup = $this->createFullSetup();
        $currentMonth = Carbon::now()->format('Y-m');

        $monthly = $this->createMonthly($setup['lease'], $currentMonth, [
            'total_due' => 150000, 'paid_amount' => 150000, 'remaining_amount' => 0, 'status' => 'paye',
        ]);

        Payment::factory()->create([
            'lease_monthly_id' => $monthly->id, 'sci_id' => $setup['sci']->id, 'amount' => 150000,
        ]);

        $history = $this->reportService->tenantPaymentHistory($setup['tenant']->id);

        $this->assertCount(1, $history);
        $this->assertCount(1, $history->first()->payments);
    }

    public function test_tenant_payment_history_empty(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($user);
        $sci = Sci::factory()->create();
        $tenant = Tenant::factory()->create(['sci_id' => $sci->id]);

        $history = $this->reportService->tenantPaymentHistory($tenant->id);

        $this->assertCount(0, $history);
    }

    /* ------------------------------------------------------------------ */
    /*  revenueProjection()                                                 */
    /* ------------------------------------------------------------------ */

    public function test_revenue_projection_structure(): void
    {
        $result = $this->reportService->revenueProjection();

        $this->assertArrayHasKey('historical', $result);
        $this->assertArrayHasKey('projected', $result);
        $this->assertCount(6, $result['historical']);
        $this->assertCount(6, $result['projected']);
    }

    public function test_revenue_projection_with_active_lease(): void
    {
        $setup = $this->createFullSetup(['rent_amount' => 200000]);

        // Ensure lease extends well into the future for projection
        $setup['lease']->update(['end_date' => now()->addYears(2)]);

        $result = $this->reportService->revenueProjection($setup['sci']->id);

        // Each projected month should include the active lease rent
        foreach ($result['projected'] as $entry) {
            $this->assertEquals(200000, $entry['projected']);
        }
    }
}
