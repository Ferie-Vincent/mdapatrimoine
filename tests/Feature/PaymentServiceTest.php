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
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = app()->make(PaymentService::class);
    }

    /**
     * Helper to create a monthly with all dependencies.
     */
    private function createMonthly(array $monthlyOverrides = []): LeaseMonthly
    {
        $user = User::create([
            'name'     => 'Admin Paiement',
            'email'    => 'admin-payment@test.com',
            'password' => bcrypt('password'),
            'role'     => 'super_admin',
        ]);
        $this->actingAs($user);

        $sci = Sci::create([
            'name'    => 'SCI Paiements',
            'rccm'    => 'RCCM-2025-003',
            'ifu'     => 'IFU-33333',
            'address' => 'Cotonou, Bénin',
        ]);

        $property = Property::create([
            'sci_id'    => $sci->id,
            'reference' => 'APT-P01',
            'type'      => 'appartement',
            'address'   => '20 Rue Paiement, Cotonou',
            'city'      => 'Cotonou',
            'status'    => 'occupe',
        ]);

        $tenant = Tenant::create([
            'sci_id'     => $sci->id,
            'first_name' => 'Paul',
            'last_name'  => 'Mensah',
            'email'      => 'paul.mensah@email.com',
            'phone'      => '+229 94 00 00 00',
        ]);

        $lease = Lease::create([
            'sci_id'          => $sci->id,
            'property_id'     => $property->id,
            'tenant_id'       => $tenant->id,
            'start_date'      => '2025-01-01',
            'end_date'        => '2025-12-31',
            'duration_months' => 12,
            'rent_amount'     => 100000,
            'charges_amount'  => 10000,
            'deposit_amount'  => 200000,
            'due_day'         => 5,
            'status'          => 'actif',
        ]);

        $defaults = [
            'lease_id'         => $lease->id,
            'sci_id'           => $sci->id,
            'month'            => '2025-01',
            'rent_due'         => 100000,
            'charges_due'      => 10000,
            'penalty_due'      => 0,
            'total_due'        => 110000,
            'paid_amount'      => 0,
            'remaining_amount' => 110000,
            'status'           => 'impaye',
            'due_date'         => '2025-01-05',
        ];

        return LeaseMonthly::create(array_merge($defaults, $monthlyOverrides));
    }

    public function test_full_payment_marks_monthly_as_paye(): void
    {
        $monthly = $this->createMonthly();

        $payment = $this->paymentService->recordPayment($monthly, [
            'amount'    => 110000,
            'paid_at'   => '2025-01-03',
            'method'    => 'especes',
            'reference' => 'REF-001',
        ]);

        // Assert payment record created
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertDatabaseHas('payments', [
            'id'               => $payment->id,
            'lease_monthly_id' => $monthly->id,
            'amount'           => '110000.00',
            'method'           => 'especes',
        ]);

        // Assert monthly is fully paid
        $monthly->refresh();
        $this->assertEquals('paye', $monthly->status);
        $this->assertEquals('110000.00', $monthly->paid_amount);
        $this->assertEquals('0.00', $monthly->remaining_amount);
    }

    public function test_partial_payment_marks_monthly_as_partiel(): void
    {
        $monthly = $this->createMonthly();

        $payment = $this->paymentService->recordPayment($monthly, [
            'amount'  => 50000,
            'paid_at' => '2025-01-03',
            'method'  => 'virement',
        ]);

        $this->assertInstanceOf(Payment::class, $payment);

        // Assert monthly is partially paid
        $monthly->refresh();
        $this->assertEquals('partiel', $monthly->status);
        $this->assertEquals('50000.00', $monthly->paid_amount);
        $this->assertEquals('60000.00', $monthly->remaining_amount);
    }

    public function test_payment_exceeding_remaining_caps_remaining_at_zero(): void
    {
        $monthly = $this->createMonthly();

        // Pay more than total_due
        $payment = $this->paymentService->recordPayment($monthly, [
            'amount'  => 150000,
            'paid_at' => '2025-01-03',
            'method'  => 'especes',
        ]);

        $this->assertInstanceOf(Payment::class, $payment);

        // Remaining should be capped at 0, status should be paye
        $monthly->refresh();
        $this->assertEquals('paye', $monthly->status);
        $this->assertEquals('150000.00', $monthly->paid_amount);
        $this->assertEquals('0.00', $monthly->remaining_amount);
    }

    public function test_multiple_partial_payments_accumulate(): void
    {
        $monthly = $this->createMonthly();

        // First partial payment
        $this->paymentService->recordPayment($monthly, [
            'amount'  => 40000,
            'paid_at' => '2025-01-03',
            'method'  => 'especes',
        ]);

        $monthly->refresh();
        $this->assertEquals('partiel', $monthly->status);
        $this->assertEquals('40000.00', $monthly->paid_amount);
        $this->assertEquals('70000.00', $monthly->remaining_amount);

        // Second partial payment to complete
        $this->paymentService->recordPayment($monthly, [
            'amount'  => 70000,
            'paid_at' => '2025-01-10',
            'method'  => 'cheque',
        ]);

        $monthly->refresh();
        $this->assertEquals('paye', $monthly->status);
        $this->assertEquals('110000.00', $monthly->paid_amount);
        $this->assertEquals('0.00', $monthly->remaining_amount);

        // Assert 2 payment records exist
        $paymentCount = Payment::where('lease_monthly_id', $monthly->id)->count();
        $this->assertEquals(2, $paymentCount);
    }
}
