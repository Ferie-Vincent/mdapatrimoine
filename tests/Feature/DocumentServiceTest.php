<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Sci;
use App\Models\Tenant;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    private DocumentService $documentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->documentService = app()->make(DocumentService::class);
    }

    /**
     * Helper to create a fully populated monthly with all dependencies.
     *
     * @return array{sci: Sci, lease: Lease, monthly: LeaseMonthly, property: Property, tenant: Tenant}
     */
    private function createFullData(): array
    {
        $user = User::create([
            'name'     => 'Admin Document',
            'email'    => 'admin-doc@test.com',
            'password' => bcrypt('password'),
            'role'     => 'super_admin',
        ]);
        $this->actingAs($user);

        $sci = Sci::create([
            'name'      => 'SCI Documents Test',
            'rccm'      => 'RCCM-2025-004',
            'ifu'       => 'IFU-44444',
            'address'   => 'Cotonou, Bénin',
            'phone'     => '+229 97 00 00 00',
            'email'     => 'docs@scitest.com',
            'bank_name' => 'BOA Bénin',
            'bank_iban' => 'BJ0000000000000001',
        ]);

        $property = Property::create([
            'sci_id'    => $sci->id,
            'reference' => 'APT-D01',
            'type'      => 'appartement',
            'address'   => '30 Rue Document, Cotonou',
            'city'      => 'Cotonou',
            'status'    => 'occupe',
        ]);

        $tenant = Tenant::create([
            'sci_id'     => $sci->id,
            'first_name' => 'Aline',
            'last_name'  => 'Houeto',
            'email'      => 'aline.houeto@email.com',
            'phone'      => '+229 93 00 00 00',
            'address'    => '50 Avenue du Commerce, Cotonou',
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

        $monthly = LeaseMonthly::create([
            'lease_id'         => $lease->id,
            'sci_id'           => $sci->id,
            'month'            => '2025-01',
            'rent_due'         => 100000,
            'charges_due'      => 10000,
            'penalty_due'      => 0,
            'total_due'        => 110000,
            'paid_amount'      => 110000,
            'remaining_amount' => 0,
            'status'           => 'paye',
            'due_date'         => '2025-01-05',
        ]);

        return compact('sci', 'lease', 'monthly', 'property', 'tenant');
    }

    public function test_generate_quittance_creates_document_and_file(): void
    {
        Storage::fake('local');

        $data = $this->createFullData();

        $document = $this->documentService->generateQuittance($data['monthly']);

        // Assert Document record exists
        $this->assertInstanceOf(Document::class, $document);
        $this->assertDatabaseHas('documents', [
            'id'           => $document->id,
            'sci_id'       => $data['sci']->id,
            'type'         => 'quittance',
            'related_type' => LeaseMonthly::class,
            'related_id'   => $data['monthly']->id,
            'month'        => '2025-01',
        ]);

        // Assert file exists in storage
        Storage::disk('local')->assertExists($document->path);

        // Assert audit log
        $this->assertDatabaseHas('audit_logs', [
            'action'      => 'generated_document',
            'entity_type' => Document::class,
            'entity_id'   => $document->id,
        ]);
    }

    public function test_generate_monthly_report_creates_document_and_file(): void
    {
        Storage::fake('local');

        $data = $this->createFullData();

        // Create a second monthly for more data in the report
        LeaseMonthly::create([
            'lease_id'         => $data['lease']->id,
            'sci_id'           => $data['sci']->id,
            'month'            => '2025-02',
            'rent_due'         => 100000,
            'charges_due'      => 10000,
            'penalty_due'      => 0,
            'total_due'        => 110000,
            'paid_amount'      => 0,
            'remaining_amount' => 110000,
            'status'           => 'impaye',
            'due_date'         => '2025-02-05',
        ]);

        $document = $this->documentService->generateMonthlyReport($data['sci'], '2025-01');

        // Assert Document record exists
        $this->assertInstanceOf(Document::class, $document);
        $this->assertDatabaseHas('documents', [
            'id'     => $document->id,
            'sci_id' => $data['sci']->id,
            'type'   => 'recap_mensuel',
            'month'  => '2025-01',
        ]);

        // Assert file exists
        Storage::disk('local')->assertExists($document->path);
    }

    public function test_generate_rent_notice_creates_document(): void
    {
        Storage::fake('local');

        $data = $this->createFullData();

        // Create an unpaid monthly for the notice
        $unpaidMonthly = LeaseMonthly::create([
            'lease_id'         => $data['lease']->id,
            'sci_id'           => $data['sci']->id,
            'month'            => '2025-03',
            'rent_due'         => 100000,
            'charges_due'      => 10000,
            'penalty_due'      => 0,
            'total_due'        => 110000,
            'paid_amount'      => 0,
            'remaining_amount' => 110000,
            'status'           => 'impaye',
            'due_date'         => '2025-03-05',
        ]);

        $document = $this->documentService->generateRentNotice($unpaidMonthly);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertDatabaseHas('documents', [
            'id'           => $document->id,
            'type'         => 'avis_echeance',
            'related_type' => LeaseMonthly::class,
            'related_id'   => $unpaidMonthly->id,
        ]);

        Storage::disk('local')->assertExists($document->path);
    }

    public function test_generate_tenant_statement_creates_document(): void
    {
        Storage::fake('local');

        $data = $this->createFullData();

        // Add a second monthly
        LeaseMonthly::create([
            'lease_id'         => $data['lease']->id,
            'sci_id'           => $data['sci']->id,
            'month'            => '2025-02',
            'rent_due'         => 100000,
            'charges_due'      => 10000,
            'penalty_due'      => 0,
            'total_due'        => 110000,
            'paid_amount'      => 50000,
            'remaining_amount' => 60000,
            'status'           => 'partiel',
            'due_date'         => '2025-02-05',
        ]);

        $document = $this->documentService->generateTenantStatement(
            $data['lease'],
            '2025-01',
            '2025-02'
        );

        $this->assertInstanceOf(Document::class, $document);
        $this->assertDatabaseHas('documents', [
            'id'           => $document->id,
            'type'         => 'releve_compte',
            'related_type' => Lease::class,
            'related_id'   => $data['lease']->id,
        ]);

        Storage::disk('local')->assertExists($document->path);
    }
}
