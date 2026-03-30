<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\FixedCharge;
use App\Models\MaterialPurchase;
use App\Models\Sci;
use App\Models\ServiceProvision;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttestationController extends Controller
{
    public function show(string $type, int $id): View
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire(), 403);

        $model = match ($type) {
            'provision' => ServiceProvision::findOrFail($id),
            'purchase' => MaterialPurchase::findOrFail($id),
            'fixed-charge' => FixedCharge::findOrFail($id),
            default => abort(404),
        };

        $sci = Sci::findOrFail($model->sci_id);

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
