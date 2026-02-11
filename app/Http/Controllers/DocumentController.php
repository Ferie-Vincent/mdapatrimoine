<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Models\Sci;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Document::class);

        $sciId = $request->attributes->get('sci_id');

        $query = Document::query()->with(['related', 'sci']);

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($month = $request->input('month')) {
            $query->where('month', $month);
        }

        $documents = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('documents.index', compact('documents'));
    }

    public function show(Document $document): View
    {
        $this->authorize('view', $document);

        $document->load(['related', 'sci', 'generator']);

        return view('documents.show', compact('document'));
    }

    public function download(Document $document): StreamedResponse
    {
        $this->authorize('view', $document);

        abort_unless(Storage::exists($document->path), 404, 'Fichier introuvable.');

        $filename = $document->meta['filename'] ?? basename($document->path);

        return Storage::download($document->path, $filename);
    }

    public function preview(Document $document): StreamedResponse
    {
        $this->authorize('view', $document);

        abort_unless(Storage::exists($document->path), 404, 'Fichier introuvable.');

        return Storage::response($document->path);
    }

    public function generateQuittance(Request $request): RedirectResponse
    {
        $request->validate([
            'lease_monthly_id' => ['required', 'exists:lease_monthlies,id'],
        ]);

        $monthly = LeaseMonthly::findOrFail($request->input('lease_monthly_id'));

        $document = $this->documentService->generateQuittance($monthly);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Quittance generee avec succes.');
    }

    public function generateReceipt(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
        ]);

        $payment = Payment::findOrFail($request->input('payment_id'));

        $document = $this->documentService->generatePaymentReceipt($payment);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Recu de paiement genere avec succes.');
    }

    public function generateNotice(Request $request): RedirectResponse
    {
        $request->validate([
            'lease_monthly_id' => ['required', 'exists:lease_monthlies,id'],
        ]);

        $monthly = LeaseMonthly::findOrFail($request->input('lease_monthly_id'));

        $document = $this->documentService->generateRentNotice($monthly);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Avis d\'echeance genere avec succes.');
    }

    public function generateStatement(Request $request): RedirectResponse
    {
        $request->validate([
            'lease_id' => ['required', 'exists:leases,id'],
            'from_month' => ['required', 'date_format:Y-m'],
            'to_month' => ['required', 'date_format:Y-m', 'after_or_equal:from_month'],
        ]);

        $lease = Lease::findOrFail($request->input('lease_id'));

        $document = $this->documentService->generateTenantStatement(
            $lease,
            $request->input('from_month'),
            $request->input('to_month')
        );

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Releve locataire genere avec succes.');
    }

    public function generateMonthlyReport(Request $request): RedirectResponse
    {
        $request->validate([
            'sci_id' => ['required', 'exists:scis,id'],
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $sci = Sci::findOrFail($request->input('sci_id'));

        $document = $this->documentService->generateMonthlyReport(
            $sci,
            $request->input('month')
        );

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Rapport mensuel genere avec succes.');
    }

    public function generateAttestation(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:attestation_location,attestation_reception_fonds,attestation_bail,attestation_sortie'],
            'lease_id' => ['required', 'exists:leases,id'],
        ]);

        $lease = Lease::findOrFail($request->input('lease_id'));

        $document = $this->documentService->generateAttestation(
            $request->input('type'),
            $lease
        );

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Attestation generee avec succes.');
    }

    public function generateFicheLocataire(Request $request): RedirectResponse
    {
        $request->validate([
            'lease_id' => ['required', 'exists:leases,id'],
        ]);

        $lease = Lease::findOrFail($request->input('lease_id'));

        $document = $this->documentService->generateFicheLocataire($lease);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Fiche locataire generee avec succes.');
    }

    public function generateRecuExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
        ]);

        $payment = Payment::findOrFail($request->input('payment_id'));

        $document = $this->documentService->generateRecuExcel($payment);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Recu Excel genere avec succes.');
    }

    public function generateQuittanceExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'lease_monthly_id' => ['required', 'exists:lease_monthlies,id'],
        ]);

        $monthly = LeaseMonthly::findOrFail($request->input('lease_monthly_id'));

        $document = $this->documentService->generateQuittanceExcel($monthly);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Quittance Excel generee avec succes.');
    }
}
