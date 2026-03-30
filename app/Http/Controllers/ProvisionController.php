<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProvisionRequest;
use App\Models\ServiceProvision;
use App\Services\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ProvisionController extends Controller
{
    public function __construct(
        private readonly FinancialService $financialService,
    ) {}

    public function store(StoreProvisionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/provisions', 'public');
        }
        unset($data['receipt']);

        $this->financialService->createProvision($data);

        return redirect()->back()->with('success', 'Prestation ajoutée avec succès.');
    }

    public function destroy(ServiceProvision $provision): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $this->financialService->deleteProvision($provision);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Prestation supprimee avec succes.']);
        }

        return redirect()->back()->with('success', 'Prestation supprimée.');
    }
}
