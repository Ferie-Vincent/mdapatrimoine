<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lease;
use App\Models\LeaseMonthly;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MonthlyGenerationService
{
    /**
     * Generate LeaseMonthly records for a given lease.
     *
     * Generates from lease start_date up to the earliest of:
     * - $upToMonth (if provided, format YYYY-MM)
     * - lease end_date
     * - start_date + 12 months (if no end_date and no upToMonth)
     */
    public function generateForLease(Lease $lease, ?string $upToMonth = null): Collection
    {
        $startDate = Carbon::parse($lease->start_date)->startOfMonth();

        // Determine the upper bound month
        if ($upToMonth !== null) {
            $endDate = Carbon::createFromFormat('Y-m', $upToMonth)->startOfMonth();
        } elseif ($lease->end_date !== null) {
            $endDate = Carbon::parse($lease->end_date)->startOfMonth();
        } else {
            $endDate = (clone $startDate)->addMonths(12);
        }

        // If both upToMonth and end_date exist, cap at end_date
        if ($upToMonth !== null && $lease->end_date !== null) {
            $leaseEnd = Carbon::parse($lease->end_date)->startOfMonth();
            if ($endDate->greaterThan($leaseEnd)) {
                $endDate = $leaseEnd;
            }
        }

        $generated = collect();
        $current = clone $startDate;
        $dueDay = $lease->due_day ?? 5;

        while ($current->lessThanOrEqualTo($endDate)) {
            $monthKey = $current->format('Y-m');

            // Skip if monthly already exists for this lease+month
            $exists = LeaseMonthly::where('lease_id', $lease->id)
                ->where('month', $monthKey)
                ->exists();

            if (!$exists) {
                $rentDue = (float) $lease->rent_amount;
                $totalDue = $rentDue;

                // Calculate due_date: use the due_day, capped at max days in month
                $maxDay = $current->daysInMonth;
                $day = min($dueDay, $maxDay);
                $dueDate = Carbon::create($current->year, $current->month, $day)->toDateString();

                $monthly = LeaseMonthly::create([
                    'lease_id'         => $lease->id,
                    'sci_id'           => $lease->sci_id,
                    'month'            => $monthKey,
                    'rent_due'         => $rentDue,
                    'charges_due'      => 0,
                    'penalty_due'      => 0,
                    'total_due'        => $totalDue,
                    'paid_amount'      => 0,
                    'remaining_amount' => $totalDue,
                    'status'           => 'impaye',
                    'due_date'         => $dueDate,
                ]);

                $generated->push($monthly);
            }

            $current->addMonth();
        }

        return $generated;
    }

    /**
     * Generate monthlies for all active leases up to next month.
     *
     * @return int Number of monthlies generated.
     */
    public function generateAllPending(): int
    {
        $upToMonth = Carbon::now()->addMonth()->format('Y-m');
        $count = 0;

        $activeLeases = Lease::where('status', 'actif')->get();

        foreach ($activeLeases as $lease) {
            $generated = $this->generateForLease($lease, $upToMonth);
            $count += $generated->count();
        }

        return $count;
    }

    /**
     * Apply late payment penalties to overdue monthlies.
     *
     * For each monthly where remaining_amount > 0 and due_date + penalty_delay_days < now(),
     * apply the penalty_rate from the lease if no penalty has been applied yet (penalty_due == 0).
     *
     * @return int Number of penalties applied.
     */
    public function applyPenalties(): int
    {
        $count = 0;

        $overdueMonthlies = LeaseMonthly::whereIn('status', ['impaye', 'partiel', 'en_retard'])
            ->where('remaining_amount', '>', 0)
            ->where('due_date', '<', Carbon::now())
            ->where('penalty_due', '=', 0)
            ->with('lease')
            ->get();

        foreach ($overdueMonthlies as $monthly) {
            $lease = $monthly->lease;

            if (!$lease || $lease->penalty_rate <= 0) {
                continue;
            }

            $gracePeriodEnd = Carbon::parse($monthly->due_date)->addDays($lease->penalty_delay_days);

            if (Carbon::now()->greaterThan($gracePeriodEnd)) {
                $penaltyAmount = round(((float) $lease->rent_amount) * ((float) $lease->penalty_rate / 100), 2);

                $monthly->update([
                    'penalty_due'      => $penaltyAmount,
                    'total_due'        => (float) $monthly->total_due + $penaltyAmount,
                    'remaining_amount' => (float) $monthly->remaining_amount + $penaltyAmount,
                    'status'           => 'en_retard',
                ]);

                $count++;
            }
        }

        return $count;
    }
}
