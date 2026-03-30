<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreFixedChargeRequest;
use App\Models\FixedCharge;
use App\Services\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class FixedChargeController extends Controller
{
    public function __construct(
        private readonly FinancialService $financialService,
    ) {}

    public function store(StoreFixedChargeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/fixed-charges', 'public');
        }
        unset($data['receipt']);

        $this->financialService->createFixedCharge($data);

        return redirect()->back()->with('success', 'Charge fixe ajoutée avec succès.');
    }

    public function destroy(FixedCharge $fixedCharge): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $this->financialService->deleteFixedCharge($fixedCharge);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Charge fixe supprimee avec succes.']);
        }

        return redirect()->back()->with('success', 'Charge fixe supprimée.');
    }
}
