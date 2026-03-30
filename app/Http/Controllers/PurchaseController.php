<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\MaterialPurchase;
use App\Services\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly FinancialService $financialService,
    ) {}

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/purchases', 'public');
        }
        unset($data['receipt']);

        $this->financialService->createPurchase($data);

        return redirect()->back()->with('success', 'Achat ajouté avec succès.');
    }

    public function destroy(MaterialPurchase $purchase): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $this->financialService->deletePurchase($purchase);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Achat supprime avec succes.']);
        }

        return redirect()->back()->with('success', 'Achat supprimé.');
    }
}
