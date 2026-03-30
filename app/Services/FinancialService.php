<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FixedCharge;
use App\Models\MaterialPurchase;
use App\Models\MonthlyBudget;
use App\Models\ServiceProvision;

class FinancialService
{
    /**
     * Données agrégées du point financier courant pour un mois/année.
     *
     * @return array{provisions: \Illuminate\Database\Eloquent\Collection, purchases: \Illuminate\Database\Eloquent\Collection, fixedCharges: \Illuminate\Database\Eloquent\Collection, totalProvisions: float, totalPurchases: float, totalFixedCharges: float, totalGlobal: float, budget: ?MonthlyBudget, budgetAmount: float, soldeCaisse: float}
     */
    public function getMonthlyOverview(?int $sciId, int $month, int $year): array
    {
        $provisionQuery = ServiceProvision::query()->where('month', $month)->where('year', $year);
        $purchaseQuery = MaterialPurchase::query()->where('month', $month)->where('year', $year);
        $fixedChargesQuery = FixedCharge::where('month', $month)->where('year', $year);

        if ($sciId) {
            $provisionQuery->where('sci_id', $sciId);
            $purchaseQuery->where('sci_id', $sciId);
            $fixedChargesQuery->where('sci_id', $sciId);
        }

        $provisions = $provisionQuery->orderBy('service_date')->get();
        $purchases = $purchaseQuery->orderBy('purchase_date')->get();
        $fixedCharges = $fixedChargesQuery->orderBy('charge_type')->orderBy('created_at')->get();

        $totalProvisions = (float) $provisions->sum('amount');
        $totalPurchases = (float) $purchases->sum('amount');
        $totalFixedCharges = (float) $fixedCharges->sum('amount');
        $totalGlobal = $totalProvisions + $totalPurchases + $totalFixedCharges;

        $budget = $sciId
            ? MonthlyBudget::where('sci_id', $sciId)->where('month', $month)->where('year', $year)->first()
            : null;
        $budgetAmount = (float) ($budget->amount ?? 0);
        $soldeCaisse = $budgetAmount - $totalGlobal;

        return compact(
            'provisions', 'purchases', 'fixedCharges',
            'totalProvisions', 'totalPurchases', 'totalFixedCharges', 'totalGlobal',
            'budget', 'budgetAmount', 'soldeCaisse',
        );
    }

    public function createProvision(array $data): ServiceProvision
    {
        $provision = ServiceProvision::create($data);

        AuditService::log('created', $provision, $data);

        return $provision;
    }

    public function deleteProvision(ServiceProvision $provision): void
    {
        $provision->delete();

        AuditService::log('deleted', $provision);
    }

    public function createPurchase(array $data): MaterialPurchase
    {
        $purchase = MaterialPurchase::create($data);

        AuditService::log('created', $purchase, $data);

        return $purchase;
    }

    public function deletePurchase(MaterialPurchase $purchase): void
    {
        $purchase->delete();

        AuditService::log('deleted', $purchase);
    }

    public function createFixedCharge(array $data): FixedCharge
    {
        $charge = FixedCharge::create($data);

        AuditService::log('created', $charge, $data);

        return $charge;
    }

    public function deleteFixedCharge(FixedCharge $fixedCharge): void
    {
        $fixedCharge->delete();

        AuditService::log('deleted', $fixedCharge);
    }

    public function upsertBudget(array $data): MonthlyBudget
    {
        return MonthlyBudget::updateOrCreate(
            [
                'sci_id' => $data['sci_id'],
                'month'  => $data['month'],
                'year'   => $data['year'],
            ],
            ['amount' => $data['amount'], 'type' => 'global'],
        );
    }
}
