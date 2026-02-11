<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tenant::class);

        $sciId = $request->attributes->get('sci_id');

        $query = Tenant::query()->with('sci');

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $tenants = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('tenants.index', compact('tenants'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('tenants.index');
    }

    public function store(StoreTenantRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Tenant::class);

        $data = $request->validated();

        if ($request->hasFile('id_file')) {
            $data['id_file_path'] = $request->file('id_file')->store('tenants/id_files', 'public');
        }

        if ($request->hasFile('id_file_verso')) {
            $data['id_file_verso_path'] = $request->file('id_file_verso')->store('tenants/id_files', 'public');
        }

        if ($request->hasFile('payment_receipt')) {
            $data['payment_receipt_path'] = $request->file('payment_receipt')->store('tenants/payment_receipts', 'public');
        }

        $tenant = Tenant::create($data);

        AuditService::log('created', $tenant, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Locataire cree avec succes.']);
        }

        return redirect()
            ->route('tenants.show', $tenant)
            ->with('success', 'Locataire cree avec succes.');
    }

    public function show(Tenant $tenant): View
    {
        $this->authorize('view', $tenant);

        $tenant->load('sci');

        $leaseHistory = $tenant->leases()
            ->with(['property', 'leaseMonthlies'])
            ->orderByDesc('start_date')
            ->get();

        $currentLease = $leaseHistory->where('status', 'actif')->first();

        return view('tenants.show', compact('tenant', 'currentLease', 'leaseHistory'));
    }

    public function edit(Tenant $tenant): RedirectResponse
    {
        return redirect()->route('tenants.show', $tenant);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $tenant);

        $data = $request->validated();

        if ($request->hasFile('id_file')) {
            $data['id_file_path'] = $request->file('id_file')->store('tenants/id_files', 'public');
        }

        if ($request->hasFile('id_file_verso')) {
            $data['id_file_verso_path'] = $request->file('id_file_verso')->store('tenants/id_files', 'public');
        }

        if ($request->hasFile('payment_receipt')) {
            $data['payment_receipt_path'] = $request->file('payment_receipt')->store('tenants/payment_receipts', 'public');
        }

        $tenant->update($data);

        AuditService::log('updated', $tenant, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Locataire mis a jour avec succes.']);
        }

        return redirect()
            ->route('tenants.show', $tenant)
            ->with('success', 'Locataire mis a jour avec succes.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->authorize('delete', $tenant);

        $tenant->delete();

        AuditService::log('deleted', $tenant);

        return redirect()
            ->route('tenants.index')
            ->with('success', 'Locataire supprime avec succes.');
    }
}
