<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProviderContractRequest;
use App\Http\Requests\UpdateProviderContractRequest;
use App\Models\ProviderContract;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;

class ProviderContractController extends Controller
{
    public function store(StoreProviderContractRequest $request): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $contract = ProviderContract::create($request->validated());

        AuditService::log('created', $contract, $request->validated());

        return redirect()
            ->route('service-providers.index')
            ->with('success', 'Contrat cree avec succes.');
    }

    public function update(UpdateProviderContractRequest $request, ProviderContract $contract): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $contract->update($request->validated());

        AuditService::log('updated', $contract, $request->validated());

        return redirect()
            ->route('service-providers.index')
            ->with('success', 'Contrat mis a jour avec succes.');
    }

    public function destroy(ProviderContract $contract): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $contract->delete();

        AuditService::log('deleted', $contract);

        return redirect()
            ->route('service-providers.index')
            ->with('success', 'Contrat supprime avec succes.');
    }
}
