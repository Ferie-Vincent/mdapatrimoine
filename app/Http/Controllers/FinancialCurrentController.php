<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\FixedCharge;
use App\Models\MaterialPurchase;
use App\Models\MonthlyBudget;
use App\Models\Sci;
use App\Models\ServiceProvision;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FinancialCurrentController extends Controller
{
    public function index(Request $request): View
    {
        $sciId = $request->attributes->get('sci_id');
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $provisionQuery = ServiceProvision::query()->where('month', $month)->where('year', $year);
        $purchaseQuery = MaterialPurchase::query()->where('month', $month)->where('year', $year);

        if ($sciId) {
            $provisionQuery->where('sci_id', $sciId);
            $purchaseQuery->where('sci_id', $sciId);
        }

        $provisions = $provisionQuery->orderBy('service_date')->get();
        $purchases = $purchaseQuery->orderBy('purchase_date')->get();

        $totalProvisions = (float) $provisions->sum('amount');
        $totalPurchases = (float) $purchases->sum('amount');

        // Fixed charges
        $fixedChargesQuery = FixedCharge::where('month', $month)->where('year', $year);
        if ($sciId) {
            $fixedChargesQuery->where('sci_id', $sciId);
        }
        $fixedCharges = $fixedChargesQuery->orderBy('charge_type')->orderBy('created_at')->get();
        $totalFixedCharges = (float) $fixedCharges->sum('amount');

        // Global total includes all three
        $totalGlobal = $totalProvisions + $totalPurchases + $totalFixedCharges;

        // Single unified budget (Caisse)
        $budget = $sciId
            ? MonthlyBudget::where('sci_id', $sciId)->where('month', $month)->where('year', $year)->first()
            : null;
        $budgetAmount = (float) ($budget->amount ?? 0);
        $soldeCaisse = $budgetAmount - $totalGlobal;

        return view('excel.financial-current', compact(
            'provisions', 'purchases', 'month', 'year',
            'totalProvisions', 'totalPurchases', 'totalGlobal',
            'budget', 'budgetAmount', 'soldeCaisse',
            'fixedCharges', 'totalFixedCharges',
        ));
    }

    public function storeProvision(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $data = $request->validate([
            'sci_id'       => ['required', 'exists:scis,id'],
            'month'        => ['required', 'integer', 'min:1', 'max:12'],
            'year'         => ['required', 'integer', 'min:2020'],
            'service_type' => ['required', 'in:ELECTRICITE,PLOMBERIE,MENUISERIE,SERRURIE,VITRIER,CARRELAGE,PEINTURE,FERRONIER'],
            'agent'        => ['required', 'string', 'max:255'],
            'service_date' => ['nullable', 'date'],
            'amount'       => ['nullable', 'numeric', 'min:0'],
            'status'         => ['nullable', 'in:En cours,Terminé,Ajourné'],
            'payment_method' => ['nullable', 'in:especes,virement,cheque,mobile_money'],
            'receipt'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'sci_id.required'       => 'Veuillez sélectionner une SCI.',
            'service_type.required' => 'Le type de prestation est obligatoire.',
            'agent.required'        => 'Le nom de l\'agent est obligatoire.',
            'amount.numeric'        => 'Le montant doit être un nombre.',
            'receipt.mimes'         => 'Le justificatif doit être au format JPG, PNG ou PDF.',
            'receipt.max'           => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/provisions', 'public');
        }
        unset($data['receipt']);

        $provision = ServiceProvision::create($data);

        AuditService::log('created', $provision, $data);

        return redirect()->back()->with('success', 'Prestation ajoutée avec succès.');
    }

    public function destroyProvision(ServiceProvision $provision): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $provision->delete();

        AuditService::log('deleted', $provision);

        return redirect()->back()->with('success', 'Prestation supprimée.');
    }

    public function storePurchase(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $data = $request->validate([
            'sci_id'        => ['required', 'exists:scis,id'],
            'month'         => ['required', 'integer', 'min:1', 'max:12'],
            'year'          => ['required', 'integer', 'min:2020'],
            'materials'     => ['required', 'string', 'max:255'],
            'supplier'      => ['required', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'amount'         => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:especes,virement,cheque,mobile_money'],
            'receipt'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'sci_id.required'    => 'Veuillez sélectionner une SCI.',
            'materials.required' => 'La description du matériel est obligatoire.',
            'supplier.required'  => 'Le nom du fournisseur est obligatoire.',
            'amount.numeric'     => 'Le montant doit être un nombre.',
            'receipt.mimes'      => 'Le justificatif doit être au format JPG, PNG ou PDF.',
            'receipt.max'        => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/purchases', 'public');
        }
        unset($data['receipt']);

        $purchase = MaterialPurchase::create($data);

        AuditService::log('created', $purchase, $data);

        return redirect()->back()->with('success', 'Achat ajouté avec succès.');
    }

    public function destroyPurchase(MaterialPurchase $purchase): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $purchase->delete();

        AuditService::log('deleted', $purchase);

        return redirect()->back()->with('success', 'Achat supprimé.');
    }

    public function storeBudget(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $data = $request->validate([
            'sci_id' => ['required', 'exists:scis,id'],
            'month'  => ['required', 'integer', 'min:1', 'max:12'],
            'year'   => ['required', 'integer', 'min:2020'],
            'amount' => ['required', 'numeric', 'min:0'],
        ], [
            'sci_id.required' => 'Veuillez sélectionner une SCI.',
            'sci_id.exists'   => 'La SCI sélectionnée est invalide.',
            'amount.required' => 'Le montant du budget est obligatoire.',
            'amount.numeric'  => 'Le montant doit être un nombre.',
            'amount.min'      => 'Le montant ne peut pas être négatif.',
        ]);

        MonthlyBudget::updateOrCreate(
            [
                'sci_id' => $data['sci_id'],
                'month'  => $data['month'],
                'year'   => $data['year'],
            ],
            ['amount' => $data['amount'], 'type' => 'global'],
        );

        return redirect()->back()->with('success', 'Budget mensuel défini avec succès.');
    }

    public function storeFixedCharge(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $data = $request->validate([
            'sci_id'         => ['required', 'exists:scis,id'],
            'month'          => ['required', 'integer', 'min:1', 'max:12'],
            'year'           => ['required', 'integer', 'min:2020'],
            'charge_type'    => ['required', 'in:cie,sodeci,honoraire'],
            'label'          => ['nullable', 'string', 'max:255'],
            'amount'         => ['required', 'numeric', 'min:0'],
            'payment_date'   => ['nullable', 'date'],
            'payment_method' => ['nullable', 'in:especes,virement,cheque,mobile_money'],
            'receipt'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'sci_id.required'      => 'Veuillez sélectionner une SCI.',
            'charge_type.required' => 'Le type de charge est obligatoire.',
            'amount.required'      => 'Le montant est obligatoire.',
            'amount.numeric'       => 'Le montant doit être un nombre.',
            'receipt.mimes'        => 'Le justificatif doit être au format JPG, PNG ou PDF.',
            'receipt.max'          => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/fixed-charges', 'public');
        }
        unset($data['receipt']);

        $charge = FixedCharge::create($data);

        AuditService::log('created', $charge, $data);

        return redirect()->back()->with('success', 'Charge fixe ajoutée avec succès.');
    }

    public function destroyFixedCharge(FixedCharge $fixedCharge): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $fixedCharge->delete();

        AuditService::log('deleted', $fixedCharge);

        return redirect()->back()->with('success', 'Charge fixe supprimée.');
    }

    public function showAttestation(string $type, int $id): View
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $model = match ($type) {
            'provision' => ServiceProvision::findOrFail($id),
            'purchase' => MaterialPurchase::findOrFail($id),
            'fixed-charge' => FixedCharge::findOrFail($id),
            default => abort(404),
        };

        $sci = Sci::findOrFail($model->sci_id);

        // Build display data based on type
        $beneficiary = match ($type) {
            'provision' => $model->agent,
            'purchase' => $model->supplier,
            'fixed-charge' => $model->label ?? $model->charge_type_label,
        };

        $description = match ($type) {
            'provision' => "Prestation de service — {$model->service_type}",
            'purchase' => "Achat de matériel — {$model->materials}",
            'fixed-charge' => "Charge fixe — {$model->charge_type_label}",
        };

        $paymentMethod = $model->payment_method;
        $amount = (float) $model->amount;
        $date = match ($type) {
            'provision' => $model->service_date,
            'purchase' => $model->purchase_date,
            'fixed-charge' => $model->payment_date,
        };

        return view('excel.attestation-fonds', compact(
            'model', 'sci', 'type', 'beneficiary', 'description',
            'paymentMethod', 'amount', 'date',
        ));
    }

    public function saveSignature(Request $request, string $type, int $id): JsonResponse
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $request->validate([
            'signature_data' => ['required', 'string'],
        ]);

        $model = match ($type) {
            'provision' => ServiceProvision::findOrFail($id),
            'purchase' => MaterialPurchase::findOrFail($id),
            'fixed-charge' => FixedCharge::findOrFail($id),
            default => abort(404),
        };

        $model->update(['signature_data' => $request->input('signature_data')]);

        AuditService::log('signature_saved', $model);

        return response()->json(['success' => true]);
    }
}
