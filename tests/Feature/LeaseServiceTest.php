<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Property;
use App\Models\Sci;
use App\Models\Tenant;
use App\Models\User;
use App\Services\LeaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaseServiceTest extends TestCase
{
    use RefreshDatabase;

    private LeaseService $leaseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leaseService = app()->make(LeaseService::class);
    }

    /**
     * Helper to create base data needed for lease tests.
     *
     * @return array{sci: Sci, property: Property, tenant: Tenant, user: User}
     */
    private function createBaseData(): array
    {
        $user = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'role'     => 'super_admin',
        ]);

        $this->actingAs($user);

        $sci = Sci::create([
            'name'    => 'SCI Test Immobilier',
            'rccm'    => 'RCCM-2025-001',
            'ifu'     => 'IFU-12345',
            'address' => 'Cotonou, Bénin',
            'phone'   => '+229 97 00 00 00',
            'email'   => 'contact@scitest.com',
        ]);

        $property = Property::create([
            'sci_id'            => $sci->id,
            'reference'         => 'APT-001',
            'type'              => 'appartement',
            'address'           => '123 Rue de la Paix, Cotonou',
            'city'              => 'Cotonou',
            'status'            => 'disponible',
            'rent_reference'    => 150000,
            'charges_reference' => 15000,
        ]);

        $tenant = Tenant::create([
            'sci_id'     => $sci->id,
            'first_name' => 'Jean',
            'last_name'  => 'Dupont',
            'email'      => 'jean.dupont@email.com',
            'phone'      => '+229 96 00 00 00',
            'address'    => '456 Avenue du Commerce, Cotonou',
        ]);

        return compact('sci', 'property', 'tenant', 'user');
    }

    public function test_create_lease_successfully(): void
    {
        $data = $this->createBaseData();

        $leaseData = [
            'sci_id'             => $data['sci']->id,
            'property_id'        => $data['property']->id,
            'tenant_id'          => $data['tenant']->id,
            'start_date'         => '2025-01-01',
            'end_date'           => '2025-12-31',
            'duration_months'    => 12,
            'rent_amount'        => 150000,
            'charges_amount'     => 15000,
            'deposit_amount'     => 300000,
            'payment_method'     => 'especes',
            'due_day'            => 5,
            'penalty_rate'       => 10,
            'penalty_delay_days' => 5,
            'status'             => 'actif',
        ];

        $lease = $this->leaseService->createLease($leaseData);

        // Assert lease created with correct data
        $this->assertInstanceOf(Lease::class, $lease);
        $this->assertDatabaseHas('leases', [
            'id'          => $lease->id,
            'sci_id'      => $data['sci']->id,
            'property_id' => $data['property']->id,
            'tenant_id'   => $data['tenant']->id,
            'status'      => 'actif',
            'rent_amount'  => '150000.00',
            'charges_amount' => '15000.00',
        ]);

        // Assert property status changed to 'occupe'
        $data['property']->refresh();
        $this->assertEquals('occupe', $data['property']->status);

        // Assert monthlies generated (12 months: Jan to Dec 2025)
        $monthliesCount = LeaseMonthly::where('lease_id', $lease->id)->count();
        $this->assertEquals(12, $monthliesCount);

        // Assert first monthly has correct amounts
        $firstMonthly = LeaseMonthly::where('lease_id', $lease->id)
            ->where('month', '2025-01')
            ->first();
        $this->assertNotNull($firstMonthly);
        $this->assertEquals('150000.00', $firstMonthly->rent_due);
        $this->assertEquals('15000.00', $firstMonthly->charges_due);
        $this->assertEquals('165000.00', $firstMonthly->total_due);

        // Assert audit log created
        $this->assertDatabaseHas('audit_logs', [
            'action'      => 'created',
            'entity_type' => Lease::class,
            'entity_id'   => $lease->id,
            'sci_id'      => $data['sci']->id,
        ]);
    }

    public function test_create_lease_on_occupied_property_throws_exception(): void
    {
        $data = $this->createBaseData();

        // Create a first active lease on the property
        Lease::create([
            'sci_id'             => $data['sci']->id,
            'property_id'        => $data['property']->id,
            'tenant_id'          => $data['tenant']->id,
            'start_date'         => '2025-01-01',
            'end_date'           => '2025-12-31',
            'duration_months'    => 12,
            'rent_amount'        => 150000,
            'charges_amount'     => 15000,
            'deposit_amount'     => 300000,
            'due_day'            => 5,
            'status'             => 'actif',
        ]);

        // Attempt to create a second lease on the same property
        $this->expectException(\InvalidArgumentException::class);

        $this->leaseService->createLease([
            'sci_id'             => $data['sci']->id,
            'property_id'        => $data['property']->id,
            'tenant_id'          => $data['tenant']->id,
            'start_date'         => '2025-06-01',
            'end_date'           => '2026-05-31',
            'duration_months'    => 12,
            'rent_amount'        => 150000,
            'charges_amount'     => 15000,
            'deposit_amount'     => 300000,
            'due_day'            => 5,
            'status'             => 'actif',
        ]);
    }

    public function test_terminate_lease_successfully(): void
    {
        $data = $this->createBaseData();

        $lease = $this->leaseService->createLease([
            'sci_id'             => $data['sci']->id,
            'property_id'        => $data['property']->id,
            'tenant_id'          => $data['tenant']->id,
            'start_date'         => '2025-01-01',
            'end_date'           => '2025-12-31',
            'duration_months'    => 12,
            'rent_amount'        => 150000,
            'charges_amount'     => 15000,
            'deposit_amount'     => 300000,
            'due_day'            => 5,
            'status'             => 'actif',
        ]);

        // Verify property is occupied before termination
        $data['property']->refresh();
        $this->assertEquals('occupe', $data['property']->status);

        // Terminate the lease
        $terminatedLease = $this->leaseService->terminateLease($lease, [
            'termination_date'   => '2025-06-30',
            'termination_reason' => 'Fin anticipée par le locataire',
        ]);

        // Assert lease status is 'resilie'
        $this->assertEquals('resilie', $terminatedLease->status);
        $this->assertEquals('2025-06-30', $terminatedLease->termination_date->format('Y-m-d'));
        $this->assertEquals('Fin anticipée par le locataire', $terminatedLease->termination_reason);

        // Assert property back to 'disponible'
        $data['property']->refresh();
        $this->assertEquals('disponible', $data['property']->status);

        // Assert audit log for termination
        $this->assertDatabaseHas('audit_logs', [
            'action'      => 'terminated',
            'entity_type' => Lease::class,
            'entity_id'   => $lease->id,
        ]);
    }
}
