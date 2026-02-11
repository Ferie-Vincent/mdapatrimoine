<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Models\Sci;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Generate a quittance de loyer PDF for a given monthly.
     */
    public function generateQuittance(LeaseMonthly $monthly): Document
    {
        $monthly->load(['lease.tenant', 'lease.property', 'lease.sci']);
        $lease = $monthly->lease;

        $data = [
            'monthly'  => $monthly,
            'lease'    => $lease,
            'tenant'   => $lease->tenant,
            'property' => $lease->property,
            'sci'      => $lease->sci,
        ];

        $filename = "quittance_{$monthly->month}_{$lease->id}.pdf";

        return $this->storePdf(
            'pdf.quittance',
            $data,
            $filename,
            (int) $lease->sci_id,
            'quittance',
            $monthly->month,
            LeaseMonthly::class,
            $monthly->id
        );
    }

    /**
     * Generate a payment receipt PDF.
     */
    public function generatePaymentReceipt(Payment $payment): Document
    {
        $payment->load(['leaseMonthly.lease.tenant', 'leaseMonthly.lease.property', 'leaseMonthly.lease.sci']);
        $monthly = $payment->leaseMonthly;
        $lease = $monthly->lease;

        $data = [
            'payment'  => $payment,
            'monthly'  => $monthly,
            'lease'    => $lease,
            'tenant'   => $lease->tenant,
            'property' => $lease->property,
            'sci'      => $lease->sci,
        ];

        $filename = "recu_{$payment->id}_{$monthly->month}.pdf";

        return $this->storePdf(
            'pdf.payment_receipt',
            $data,
            $filename,
            (int) $lease->sci_id,
            'recu',
            $monthly->month,
            Payment::class,
            $payment->id
        );
    }

    /**
     * Generate an avis d'echeance (rent notice) PDF.
     */
    public function generateRentNotice(LeaseMonthly $monthly): Document
    {
        $monthly->load(['lease.tenant', 'lease.property', 'lease.sci']);
        $lease = $monthly->lease;

        $data = [
            'monthly'  => $monthly,
            'lease'    => $lease,
            'tenant'   => $lease->tenant,
            'property' => $lease->property,
            'sci'      => $lease->sci,
        ];

        $filename = "avis_echeance_{$monthly->month}_{$lease->id}.pdf";

        return $this->storePdf(
            'pdf.rent_notice',
            $data,
            $filename,
            (int) $lease->sci_id,
            'avis_echeance',
            $monthly->month,
            LeaseMonthly::class,
            $monthly->id
        );
    }

    /**
     * Generate a tenant statement (releve de compte) PDF.
     */
    public function generateTenantStatement(Lease $lease, string $fromMonth, string $toMonth): Document
    {
        $lease->load(['tenant', 'property', 'sci']);

        $monthlies = LeaseMonthly::where('lease_id', $lease->id)
            ->where('month', '>=', $fromMonth)
            ->where('month', '<=', $toMonth)
            ->orderBy('month')
            ->with('payments')
            ->get();

        $totalDue = $monthlies->sum('total_due');
        $totalPaid = $monthlies->sum('paid_amount');
        $totalRemaining = $monthlies->sum('remaining_amount');

        $data = [
            'lease'          => $lease,
            'tenant'         => $lease->tenant,
            'property'       => $lease->property,
            'sci'            => $lease->sci,
            'monthlies'      => $monthlies,
            'from_month'     => $fromMonth,
            'to_month'       => $toMonth,
            'total_due'      => $totalDue,
            'total_paid'     => $totalPaid,
            'total_remaining'=> $totalRemaining,
        ];

        $filename = "releve_compte_{$lease->id}_{$fromMonth}_{$toMonth}.pdf";

        return $this->storePdf(
            'pdf.tenant_statement',
            $data,
            $filename,
            (int) $lease->sci_id,
            'releve_compte',
            $fromMonth,
            Lease::class,
            $lease->id
        );
    }

    /**
     * Generate a monthly recap report for an SCI.
     */
    public function generateMonthlyReport(Sci $sci, string $month): Document
    {
        $monthlies = LeaseMonthly::where('sci_id', $sci->id)
            ->where('month', $month)
            ->with(['lease.tenant', 'lease.property', 'payments'])
            ->get();

        $totalExpected = $monthlies->sum('total_due');
        $totalCollected = $monthlies->sum('paid_amount');
        $totalRemaining = $monthlies->sum('remaining_amount');

        $data = [
            'sci'             => $sci,
            'month'           => $month,
            'monthlies'       => $monthlies,
            'total_expected'  => $totalExpected,
            'total_collected' => $totalCollected,
            'total_remaining' => $totalRemaining,
        ];

        $filename = "recap_mensuel_{$sci->id}_{$month}.pdf";

        return $this->storePdf(
            'pdf.monthly_report',
            $data,
            $filename,
            (int) $sci->id,
            'recap_mensuel',
            $month,
            Sci::class,
            $sci->id
        );
    }

    /**
     * Generate an attestation PDF.
     *
     * Supported types: attestation_location, attestation_reception_fonds,
     * attestation_bail, attestation_sortie.
     */
    public function generateAttestation(string $type, Lease $lease, array $extra = []): Document
    {
        $lease->load(['tenant', 'property', 'sci']);

        $allowedTypes = [
            'attestation_location',
            'attestation_reception_fonds',
            'attestation_bail',
            'attestation_sortie',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            throw new \InvalidArgumentException("Type d'attestation invalide: {$type}");
        }

        $data = array_merge([
            'type'     => $type,
            'lease'    => $lease,
            'tenant'   => $lease->tenant,
            'property' => $lease->property,
            'sci'      => $lease->sci,
        ], $extra);

        $filename = "{$type}_{$lease->id}_" . now()->format('Ymd_His') . ".pdf";

        return $this->storePdf(
            'pdf.attestation',
            $data,
            $filename,
            (int) $lease->sci_id,
            $type,
            null,
            Lease::class,
            $lease->id
        );
    }

    /**
     * Generate a FICHE LOCATAIRE Excel-style PDF for a lease.
     */
    public function generateFicheLocataire(Lease $lease): Document
    {
        $lease->load(['tenant', 'property', 'sci']);

        $data = [
            'lease'    => $lease,
            'tenant'   => $lease->tenant,
            'property' => $lease->property,
            'sci'      => $lease->sci,
        ];

        $filename = "fiche_locataire_{$lease->id}_" . now()->format('Ymd_His') . ".pdf";

        return $this->storePdf(
            'pdf.fiche-locataire-excel',
            $data,
            $filename,
            (int) $lease->sci_id,
            'fiche_locataire',
            null,
            Lease::class,
            $lease->id
        );
    }

    /**
     * Generate a RECU DE PAIEMENT Excel-style PDF for a payment.
     */
    public function generateRecuExcel(Payment $payment): Document
    {
        $payment->load(['leaseMonthly.lease.tenant', 'leaseMonthly.lease.property', 'leaseMonthly.lease.sci']);
        $monthly = $payment->leaseMonthly;
        $lease = $monthly->lease;

        $data = [
            'payment'  => $payment,
            'monthly'  => $monthly,
            'lease'    => $lease,
            'tenant'   => $lease->tenant,
            'property' => $lease->property,
            'sci'      => $lease->sci,
        ];

        $filename = "recu_excel_{$payment->id}_{$monthly->month}.pdf";

        return $this->storePdf(
            'pdf.recu-excel',
            $data,
            $filename,
            (int) $lease->sci_id,
            'recu_excel',
            $monthly->month,
            Payment::class,
            $payment->id
        );
    }

    /**
     * Generate a QUITTANCE DE LOYER Excel-style PDF for a monthly.
     */
    public function generateQuittanceExcel(LeaseMonthly $monthly): Document
    {
        $monthly->load(['lease.tenant', 'lease.property', 'lease.sci']);
        $lease = $monthly->lease;

        $data = [
            'monthly'  => $monthly,
            'lease'    => $lease,
            'tenant'   => $lease->tenant,
            'property' => $lease->property,
            'sci'      => $lease->sci,
        ];

        $filename = "quittance_excel_{$monthly->month}_{$lease->id}.pdf";

        return $this->storePdf(
            'pdf.quittance-excel',
            $data,
            $filename,
            (int) $lease->sci_id,
            'quittance_excel',
            $monthly->month,
            LeaseMonthly::class,
            $monthly->id
        );
    }

    /**
     * Generate entry receipt for caution + advance rent.
     */
    public function generateRecuEntreeCaution(Lease $lease): Document
    {
        $lease->loadMissing(['tenant', 'property', 'sci']);

        $data = [
            'lease'    => $lease,
            'tenant'   => $lease->tenant,
            'property' => $lease->property,
            'sci'      => $lease->sci,
        ];

        $filename = "recu_entree_caution_{$lease->id}.pdf";
        $month = $lease->start_date instanceof \Carbon\Carbon
            ? $lease->start_date->format('Y-m')
            : substr((string) $lease->start_date, 0, 7);

        return $this->storePdf(
            'pdf.recu-entree-caution',
            $data,
            $filename,
            (int) $lease->sci_id,
            'recu_entree_caution',
            $month,
            Lease::class,
            $lease->id
        );
    }

    /**
     * Generate entry receipt for agency fees.
     */
    public function generateRecuEntreeAgence(Lease $lease): Document
    {
        $lease->loadMissing(['tenant', 'property', 'sci']);

        $data = [
            'lease'    => $lease,
            'tenant'   => $lease->tenant,
            'property' => $lease->property,
            'sci'      => $lease->sci,
        ];

        $filename = "recu_entree_agence_{$lease->id}.pdf";
        $month = $lease->start_date instanceof \Carbon\Carbon
            ? $lease->start_date->format('Y-m')
            : substr((string) $lease->start_date, 0, 7);

        return $this->storePdf(
            'pdf.recu-entree-agence',
            $data,
            $filename,
            (int) $lease->sci_id,
            'recu_entree_agence',
            $month,
            Lease::class,
            $lease->id
        );
    }

    /**
     * Render a Blade view to PDF, store it on disk, and create a Document record.
     */
    private function storePdf(
        string $view,
        array $data,
        string $filename,
        int $sciId,
        string $type,
        ?string $month,
        ?string $relatedType,
        ?int $relatedId
    ): Document {
        // Determine year/month subdirectory
        $yearDir = $month ? substr($month, 0, 4) : now()->format('Y');
        $monthDir = $month ? substr($month, 5, 2) : now()->format('m');

        $directory = "documents/{$sciId}/{$yearDir}/{$monthDir}";
        $relativePath = "{$directory}/{$filename}";

        // Generate the PDF
        $pdf = Pdf::loadView($view, $data);

        // Ensure directory exists and store the file
        Storage::makeDirectory($directory);
        Storage::put($relativePath, $pdf->output());

        // Create Document record
        $document = Document::create([
            'sci_id'       => $sciId,
            'type'         => $type,
            'related_type' => $relatedType,
            'related_id'   => $relatedId,
            'month'        => $month,
            'path'         => $relativePath,
            'meta'         => ['filename' => $filename],
            'generated_by' => auth()->id(),
        ]);

        // Log audit
        AuditService::log(
            'generated_document',
            Document::class,
            $document->id,
            ['type' => $type, 'filename' => $filename],
            $sciId
        );

        return $document;
    }
}
