<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Record a payment against a lease monthly.
     *
     * Creates the payment record, updates the monthly's paid_amount, remaining_amount,
     * and status accordingly, then logs an audit entry.
     */
    public function recordPayment(LeaseMonthly $monthly, array $data): Payment
    {
        return DB::transaction(function () use ($monthly, $data): Payment {
            // Lock the monthly row to prevent concurrent overpayments
            $monthly = LeaseMonthly::lockForUpdate()->findOrFail($monthly->id);

            $payment = Payment::create([
                'lease_monthly_id' => $monthly->id,
                'sci_id'           => $monthly->sci_id,
                'amount'           => $data['amount'],
                'paid_at'          => $data['paid_at'] ?? now()->toDateString(),
                'method'           => $data['method'] ?? 'especes',
                'reference'        => $data['reference'] ?? null,
                'note'             => $data['note'] ?? null,
                'receipt_path'     => $data['receipt_path'] ?? null,
                'recorded_by'      => auth()->id(),
            ]);

            // Update monthly amounts
            $newPaidAmount = (float) $monthly->paid_amount + (float) $data['amount'];
            $newRemainingAmount = (float) $monthly->total_due - $newPaidAmount;

            // Ensure remaining does not go below zero
            if ($newRemainingAmount < 0) {
                $newRemainingAmount = 0;
            }

            // Determine new status
            if ($newRemainingAmount <= 0) {
                $newStatus = 'paye';
            } elseif ($newPaidAmount > 0) {
                $newStatus = 'partiel';
            } else {
                $newStatus = $monthly->status;
            }

            $monthly->update([
                'paid_amount'      => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
                'status'           => $newStatus,
            ]);

            // Log audit
            AuditService::log(
                'recorded_payment',
                Payment::class,
                $payment->id,
                [
                    'lease_monthly_id' => $monthly->id,
                    'amount'           => $data['amount'],
                    'method'           => $data['method'] ?? 'especes',
                ],
                $monthly->sci_id
            );

            return $payment;
        });
    }

    /**
     * Get balance summary for a lease across all its monthlies.
     *
     * @return array{total_due: float, total_paid: float, total_remaining: float}
     */
    public function getBalanceForLease(Lease $lease): array
    {
        $monthlies = LeaseMonthly::where('lease_id', $lease->id)->get();

        $totalDue = $monthlies->sum('total_due');
        $totalPaid = $monthlies->sum('paid_amount');
        $totalRemaining = $monthlies->sum('remaining_amount');

        return [
            'total_due'       => round((float) $totalDue, 2),
            'total_paid'      => round((float) $totalPaid, 2),
            'total_remaining' => round((float) $totalRemaining, 2),
        ];
    }
}
