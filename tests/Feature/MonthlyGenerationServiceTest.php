<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Property;
use App\Models\Sci;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MonthlyGenerationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    private MonthlyGenerationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app()->make(MonthlyGenerationService::class);
    }

    /**
     * Helper to create a lease with all dependencies.
     */
    private function createLease(array $overrides = []): Lease
    {
        $user = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin-monthly@test.com',
            'password' => bcrypt('password'),
            'role'     => 'super_admin',
        ]);
        $this->actingAs($user);

        $sci = Sci::create([
            'name'    => 'SCI Mensualites',
            'rccm'    => 'RCCM-2025-002',
            'ifu'     => 'IFU-22222',
            'address' => 'Cotonou, Bénin',
        ]);

        $property = Property::create([
            'sci_id'    => $sci->id,
            'reference' => 'APT-M01',
            'type'      => 'appartement',
            'address'   => '10 Rue du Test, Cotonou',
            'city'      => 'Cotonou',
            'status'    => 'occupe',
        ]);

        $tenant = Tenant::create([
            'sci_id'     => $sci->id,
            'first_name' => 'Marie',
            'last_name'  => 'Konan',
            'email'      => 'marie.konan@email.com',
            'phone'      => '+229 95 00 00 00',
        ]);

        $defaults = [
            'sci_id'             => $sci->id,
            'property_id'        => $property->id,
            'tenant_id'          => $tenant->id,
            'start_date'         => '2025-01-01',
            'end_date'           => '2025-06-30',
            'duration_months'    => 6,
            'rent_amount'        => 100000,
            'charges_amount'     => 10000,
            'deposit_amount'     => 200000,
            'due_day'            => 5,
            'penalty_rate'       => 10,
            'penalty_delay_days' => 5,
            'status'             => 'actif',
        ];

        return Lease::create(array_merge($defaults, $overrides));
    }

    public function test_generate_monthlies_for_lease(): void
    {
        $lease = $this->createLease();

        $generated = $this->service->generateForLease($lease);

        // Jan 2025 to Jun 2025 = 6 months
        $this->assertCount(6, $generated);

        // Verify records in DB
        $count = LeaseMonthly::where('lease_id', $lease->id)->count();
        $this->assertEquals(6, $count);

        // Verify months are correct
        $months = LeaseMonthly::where('lease_id', $lease->id)
            ->orderBy('month')
            ->pluck('month')
            ->toArray();

        $this->assertEquals([
            '2025-01', '2025-02', '2025-03',
            '2025-04', '2025-05', '2025-06',
        ], $months);
    }

    public function test_monthlies_have_correct_amounts(): void
    {
        $lease = $this->createLease([
            'rent_amount'    => 120000,
            'charges_amount' => 15000,
        ]);

        $this->service->generateForLease($lease);

        $monthly = LeaseMonthly::where('lease_id', $lease->id)
            ->where('month', '2025-01')
            ->first();

        $this->assertNotNull($monthly);
        $this->assertEquals('120000.00', $monthly->rent_due);
        $this->assertEquals('15000.00', $monthly->charges_due);
        $this->assertEquals('135000.00', $monthly->total_due);
        $this->assertEquals('0.00', $monthly->paid_amount);
        $this->assertEquals('135000.00', $monthly->remaining_amount);
        $this->assertEquals('impaye', $monthly->status);
    }

    public function test_duplicate_generation_does_not_create_duplicates(): void
    {
        $lease = $this->createLease();

        // Generate first time
        $firstGeneration = $this->service->generateForLease($lease);
        $this->assertCount(6, $firstGeneration);

        // Generate again for the same period
        $secondGeneration = $this->service->generateForLease($lease);
        $this->assertCount(0, $secondGeneration);

        // Total in DB should still be 6
        $count = LeaseMonthly::where('lease_id', $lease->id)->count();
        $this->assertEquals(6, $count);
    }

    public function test_apply_penalties_on_overdue_monthlies(): void
    {
        $lease = $this->createLease([
            'start_date'         => '2024-01-01',
            'end_date'           => '2024-03-31',
            'duration_months'    => 3,
            'rent_amount'        => 100000,
            'charges_amount'     => 10000,
            'penalty_rate'       => 10,
            'penalty_delay_days' => 5,
            'due_day'            => 5,
        ]);

        // Generate monthlies for past months
        $this->service->generateForLease($lease);

        // Verify monthlies are created with penalty_due = 0
        $monthlies = LeaseMonthly::where('lease_id', $lease->id)->get();
        foreach ($monthlies as $m) {
            $this->assertEquals('0.00', $m->penalty_due);
        }

        // Travel to well after grace period (past due_date + penalty_delay_days)
        Carbon::setTestNow(Carbon::parse('2024-06-01'));

        $penaltiesApplied = $this->service->applyPenalties();

        // All 3 monthlies should have penalties applied
        $this->assertEquals(3, $penaltiesApplied);

        // Check a specific monthly: penalty = rent_amount * penalty_rate / 100 = 100000 * 10 / 100 = 10000
        $monthly = LeaseMonthly::where('lease_id', $lease->id)
            ->where('month', '2024-01')
            ->first();

        $this->assertEquals('10000.00', $monthly->penalty_due);
        // total_due should be original (110000) + penalty (10000) = 120000
        $this->assertEquals('120000.00', $monthly->total_due);
        $this->assertEquals('en_retard', $monthly->status);

        Carbon::setTestNow(); // Reset time
    }
}
