<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceProviderRequest;
use App\Http\Requests\UpdateServiceProviderRequest;
use App\Models\ProviderContract;
use App\Models\ServiceProvider;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceProviderController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ServiceProvider::class);

        $sciId = $request->attributes->get('sci_id');

        $query = ServiceProvider::query()->with('sci');

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('specialty', 'like', "%{$search}%");
            });
        }

        $providers = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Contracts
        $contractsQuery = ProviderContract::with('serviceProvider')
            ->when($sciId, fn ($q) => $q->where('sci_id', $sciId));

        $contracts = $contractsQuery->orderByDesc('start_date')->get();
        $expiringContracts = ProviderContract::expiringSoon()
            ->when($sciId, fn ($q) => $q->where('sci_id', $sciId))
            ->with('serviceProvider')
            ->get();

        // All providers for contract form select
        $allProviders = ServiceProvider::query()
            ->when($sciId, fn ($q) => $q->where('sci_id', $sciId))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('service-providers.index', compact('providers', 'contracts', 'expiringContracts', 'allProviders'));
    }

    public function store(StoreServiceProviderRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', ServiceProvider::class);

        $data = $request->validated();
        $data['is_active'] = $data['is_active'] ?? true;

        $provider = ServiceProvider::create($data);

        AuditService::log('created', $provider, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Prestataire cree avec succes.']);
        }

        return redirect()
            ->route('service-providers.index')
            ->with('success', 'Prestataire cree avec succes.');
    }

    public function update(UpdateServiceProviderRequest $request, ServiceProvider $provider): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $provider);

        $data = $request->validated();
        $data['is_active'] = $data['is_active'] ?? false;

        $provider->update($data);

        AuditService::log('updated', $provider, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Prestataire mis a jour avec succes.']);
        }

        return redirect()
            ->route('service-providers.index')
            ->with('success', 'Prestataire mis a jour avec succes.');
    }

    public function destroy(ServiceProvider $provider): RedirectResponse
    {
        $this->authorize('delete', $provider);

        $provider->delete();

        AuditService::log('deleted', $provider);

        return redirect()
            ->route('service-providers.index')
            ->with('success', 'Prestataire supprime avec succes.');
    }
}
