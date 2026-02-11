<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\LeasesExport;
use App\Exports\MonthliesExport;
use App\Exports\PaymentsExport;
use App\Exports\PropertiesExport;
use App\Exports\ScisExport;
use App\Exports\ServiceProvidersExport;
use App\Exports\StaffExport;
use App\Exports\StaffPayrollExport;
use App\Exports\TenantsExport;
use App\Exports\UnpaidExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    public function exportTenants(Request $request): BinaryFileResponse|Response
    {
        $this->authorize('viewAny', \App\Models\Tenant::class);

        $sciId = $request->attributes->get('sci_id');
        $export = new TenantsExport($sciId ? (int) $sciId : null);

        return $this->download($export, 'locataires_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    public function exportProperties(Request $request): BinaryFileResponse|Response
    {
        $this->authorize('viewAny', \App\Models\Property::class);

        $sciId = $request->attributes->get('sci_id');
        $export = new PropertiesExport($sciId ? (int) $sciId : null);

        return $this->download($export, 'biens_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    public function exportPayments(Request $request): BinaryFileResponse|Response
    {
        $this->authorize('viewAny', \App\Models\Payment::class);

        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $sciId = $request->attributes->get('sci_id');
        $export = new PaymentsExport(
            $sciId ? (int) $sciId : null,
            $request->input('from_date'),
            $request->input('to_date')
        );

        return $this->download($export, 'paiements_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    public function exportUnpaid(Request $request): BinaryFileResponse|Response
    {
        $this->authorize('viewAny', \App\Models\LeaseMonthly::class);

        $sciId = $request->attributes->get('sci_id');
        $export = new UnpaidExport($sciId ? (int) $sciId : null);

        return $this->download($export, 'impayes_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    public function exportLeases(Request $request): BinaryFileResponse|Response
    {
        $this->authorize('viewAny', \App\Models\Lease::class);

        $sciId = $request->attributes->get('sci_id');
        $export = new LeasesExport($sciId ? (int) $sciId : null);

        return $this->download($export, 'baux_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    public function exportMonthlies(Request $request): BinaryFileResponse|Response
    {
        $this->authorize('viewAny', \App\Models\LeaseMonthly::class);

        $sciId = $request->attributes->get('sci_id');
        $export = new MonthliesExport(
            $sciId ? (int) $sciId : null,
            $request->input('month'),
            $request->input('status')
        );

        return $this->download($export, 'echeances_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    public function exportServiceProviders(Request $request): BinaryFileResponse|Response
    {
        $sciId = $request->attributes->get('sci_id');
        $export = new ServiceProvidersExport($sciId ? (int) $sciId : null);

        return $this->download($export, 'prestataires_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    public function exportStaff(Request $request): BinaryFileResponse|Response
    {
        $sciId = $request->attributes->get('sci_id');
        $export = new StaffExport($sciId ? (int) $sciId : null);

        return $this->download($export, 'personnel_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    public function exportStaffPayroll(Request $request): BinaryFileResponse|Response
    {
        $sciId = $request->attributes->get('sci_id');
        $export = new StaffPayrollExport(
            $sciId ? (int) $sciId : null,
            $request->input('month') ? (int) $request->input('month') : null,
            $request->input('year') ? (int) $request->input('year') : null
        );

        return $this->download($export, 'paie_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    public function exportScis(Request $request): BinaryFileResponse|Response
    {
        $this->authorize('viewAny', \App\Models\User::class);

        $export = new ScisExport();

        return $this->download($export, 'scis_' . now()->format('Y-m-d'), $request->input('format', 'xlsx'));
    }

    private function download(object $export, string $filename, string $format): BinaryFileResponse|Response
    {
        if ($format === 'pdf') {
            return $this->downloadPdf($export, $filename);
        }

        $extension = $format === 'csv' ? 'csv' : 'xlsx';
        $writerType = $format === 'csv'
            ? \Maatwebsite\Excel\Excel::CSV
            : \Maatwebsite\Excel\Excel::XLSX;

        return Excel::download($export, "{$filename}.{$extension}", $writerType);
    }

    private function downloadPdf(object $export, string $filename): Response
    {
        $headings = $export->headings();
        $rows = $export->query()->get()->map(fn ($item) => $export->map($item))->toArray();

        $titleMap = [
            'locataires' => 'Locataires',
            'biens' => 'Biens immobiliers',
            'paiements' => 'Paiements',
            'impayes' => 'Impayés',
            'baux' => 'Baux',
            'echeances' => 'Échéances mensuelles',
            'prestataires' => 'Prestataires',
            'personnel' => 'Personnel',
            'paie' => 'Paie',
            'scis' => 'SCIs',
        ];

        $prefix = explode('_', $filename)[0];
        $title = $titleMap[$prefix] ?? 'Export';

        $pdf = Pdf::loadView('exports.table-pdf', compact('headings', 'rows', 'title'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("{$filename}.pdf");
    }
}
