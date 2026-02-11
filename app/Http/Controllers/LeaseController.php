<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaseRequest;
use App\Http\Requests\UpdateLeaseRequest;
use App\Models\DepositRefund;
use App\Models\Document;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use App\Services\AuditService;
use App\Services\LeaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function __construct(
        private readonly LeaseService $leaseService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lease::class);

        $sciId = $request->attributes->get('sci_id');

        $query = Lease::query()->with(['property', 'tenant', 'sci']);

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        } else {
            $query->whereNotIn('status', ['resilie', 'expire']);
        }

        if ($propertyId = $request->input('property_id')) {
            $query->where('property_id', $propertyId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('tenant', function ($tq) use ($search) {
                    $tq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('property', function ($pq) use ($search) {
                    $pq->where('reference', 'like', "%{$search}%");
                });
            });
        }

        $leases = $query->orderByDesc('start_date')
            ->paginate(15)
            ->withQueryString();

        // Data for create modal — exclude properties already tied to an active/pending lease
        $properties = Property::query()
            ->with('sci')
            ->when($sciId, fn ($q) => $q->where('sci_id', $sciId))
            ->whereDoesntHave('leases', fn ($q) => $q->whereIn('status', ['actif', 'en_attente']))
            ->orderBy('reference')
            ->get();

        $tenants = Tenant::query()
            ->when($sciId, fn ($q) => $q->where('sci_id', $sciId))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('leases.index', compact('leases', 'properties', 'tenants'));
    }

    public function create(Request $request): RedirectResponse
    {
        return redirect()->route('leases.index');
    }

    public function store(StoreLeaseRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Lease::class);

        $data = $request->validated();

        try {
            $lease = $this->leaseService->createLease($data);

            // Upload files after successful lease creation to avoid orphans
            $updates = [];
            if ($request->hasFile('signed_lease')) {
                $updates['signed_lease_path'] = $request->file('signed_lease')->store('leases/signed', 'public');
            }
            if ($request->hasFile('entry_inspection')) {
                $updates['entry_inspection_path'] = $request->file('entry_inspection')->store('leases/inspections', 'public');
            }
            if (!empty($updates)) {
                $lease->update($updates);
            }

            AuditService::log('created', $lease, $data);
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->withErrors(['property_id' => $e->getMessage()])->withInput();
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Bail cree avec succes.']);
        }

        return redirect()
            ->route('leases.show', $lease)
            ->with('success', 'Bail cree avec succes.');
    }

    public function show(Lease $lease): View
    {
        $this->authorize('view', $lease);

        $lease->load(['property', 'tenant', 'sci']);

        $monthlies = $lease->leaseMonthlies()
            ->orderByDesc('month')
            ->paginate(15, ['*'], 'monthlies_page');

        $payments = Payment::whereHas('leaseMonthly', function ($q) use ($lease) {
                $q->where('lease_id', $lease->id);
            })
            ->with('leaseMonthly')
            ->orderByDesc('paid_at')
            ->get();

        $documents = Document::where('related_type', Lease::class)
            ->where('related_id', $lease->id)
            ->orderByDesc('created_at')
            ->get();

        $depositRefunds = DepositRefund::where('lease_id', $lease->id)->orderByDesc('refunded_at')->get();

        // Data for edit modal
        $properties = Property::where('sci_id', $lease->sci_id)
            ->orderBy('reference')
            ->get();

        $tenants = Tenant::where('sci_id', $lease->sci_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('leases.show', compact('lease', 'monthlies', 'payments', 'documents', 'depositRefunds', 'properties', 'tenants'));
    }

    public function edit(Lease $lease): RedirectResponse
    {
        return redirect()->route('leases.show', $lease);
    }

    public function update(UpdateLeaseRequest $request, Lease $lease): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $lease);

        $data = $request->validated();

        if ($request->hasFile('signed_lease')) {
            $data['signed_lease_path'] = $request->file('signed_lease')->store('leases/signed', 'public');
        }

        if ($request->hasFile('entry_inspection')) {
            $data['entry_inspection_path'] = $request->file('entry_inspection')->store('leases/inspections', 'public');
        }

        $oldRent = (float) $lease->rent_amount;

        $lease->update($data);

        // If rent changed, update unpaid monthlies (charges/frais agence not included in monthly)
        $newRent = (float) $lease->rent_amount;

        $newCharges = (float) ($lease->charges_amount ?? 0);

        if ($oldRent !== $newRent) {
            // Update fully unpaid monthlies (no payment yet)
            $lease->leaseMonthlies()
                ->where('status', 'impaye')
                ->where('paid_amount', 0)
                ->update([
                    'rent_due'         => $newRent,
                    'charges_due'      => $newCharges,
                    'total_due'        => $newRent + $newCharges,
                    'remaining_amount' => $newRent + $newCharges,
                ]);

            // Update partially paid monthlies (recalculate remaining)
            $lease->leaseMonthlies()
                ->where('status', 'partiel')
                ->where('paid_amount', '>', 0)
                ->each(function ($monthly) use ($newRent, $newCharges) {
                    $newTotal = $newRent + $newCharges + (float) $monthly->penalty_due;
                    $remaining = max(0, $newTotal - (float) $monthly->paid_amount);
                    $monthly->update([
                        'rent_due'         => $newRent,
                        'charges_due'      => $newCharges,
                        'total_due'        => $newTotal,
                        'remaining_amount' => $remaining,
                        'status'           => $remaining <= 0 ? 'paye' : 'partiel',
                    ]);
                });
        }

        AuditService::log('updated', $lease, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Bail mis a jour avec succes.']);
        }

        return redirect()
            ->route('leases.show', $lease)
            ->with('success', 'Bail mis a jour avec succes.');
    }

    public function destroy(Lease $lease): RedirectResponse
    {
        $this->authorize('delete', $lease);

        $lease->delete();

        AuditService::log('deleted', $lease);

        return redirect()
            ->route('leases.index')
            ->with('success', 'Bail supprime avec succes.');
    }

    public function terminate(Request $request, Lease $lease): RedirectResponse
    {
        $this->authorize('update', $lease);

        $request->validate([
            'termination_date'   => ['required', 'date'],
            'termination_reason' => ['nullable', 'string', 'max:1000'],
            'exit_inspection'    => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:10240'],
        ], [
            'termination_date.required' => 'La date de sortie est obligatoire.',
            'termination_date.date'     => 'La date de sortie n\'est pas valide.',
        ]);

        $data = $request->only('termination_date', 'termination_reason');

        if ($request->hasFile('exit_inspection')) {
            $data['exit_inspection_path'] = $request->file('exit_inspection')->store('leases/inspections', 'public');
        }

        $this->leaseService->terminateLease($lease, $data);

        AuditService::log('terminated', $lease, $data);

        return redirect()
            ->route('leases.show', $lease)
            ->with('success', 'Bail résilié avec succès.');
    }

    public function activate(Lease $lease): RedirectResponse
    {
        $this->authorize('update', $lease);

        $this->leaseService->activateLease($lease);

        AuditService::log('activated', $lease);

        return redirect()
            ->route('leases.show', $lease)
            ->with('success', 'Bail active avec succes.');
    }
}
