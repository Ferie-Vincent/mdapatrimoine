<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class LeaseService
{
    public function __construct(
        private readonly MonthlyGenerationService $monthlyService,
    ) {}

    /**
     * Create a new lease.
     *
     * Validates that the property has no active lease, creates the lease,
     * sets the property status to 'occupe', generates monthly records, and logs audit.
     *
     * @throws \InvalidArgumentException If the property already has an active lease.
     */
    public function createLease(array $data): Lease
    {
        return DB::transaction(function () use ($data): Lease {
            $propertyId = $data['property_id'];
            $property = Property::findOrFail($propertyId);

            // Validate: no active lease on this property
            $hasActiveLease = Lease::where('property_id', $propertyId)
                ->whereIn('status', ['actif', 'en_attente'])
                ->exists();

            if ($hasActiveLease) {
                throw new \InvalidArgumentException(
                    "Ce bien immobilier a deja un bail actif ou en attente. Impossible de creer un nouveau bail."
                );
            }

            // Branch: meuble vs classique
            if ($property->isMeuble()) {
                return $this->createMeubleLease($data, $property);
            }

            // Apply default settings if not provided
            $data['penalty_rate'] = $data['penalty_rate'] ?? (float) Setting::get('default_penalty_rate', 0);
            $data['penalty_delay_days'] = $data['penalty_delay_days'] ?? (int) Setting::get('default_penalty_delay_days', 0);
            $data['due_day'] = $data['due_day'] ?? (int) Setting::get('default_due_day', 5);

            // Create the lease
            $lease = Lease::create($data);

            // Set property status to 'occupe'
            Property::where('id', $propertyId)->update(['status' => 'occupe']);

            // Generate monthly records and entry receipts if lease is active
            if ($lease->status === 'actif') {
                $this->monthlyService->generateForLease($lease);

                // Fill entry amounts: use form values if provided, otherwise auto-calculate
                $rentAmount = (float) $lease->rent_amount;
                $lease->update([
                    'caution_2_mois'        => $data['caution_2_mois'] ?? $rentAmount * 2,
                    'loyers_avances_2_mois' => $data['loyers_avances_2_mois'] ?? $rentAmount * 2,
                    'frais_agence'          => $data['frais_agence'] ?? $rentAmount,
                    'deposit_amount'        => $data['deposit_amount'] ?? $rentAmount * 2,
                ]);

                // Generate entry receipt PDFs
                $documentService = app(DocumentService::class);
                $documentService->generateRecuEntreeCaution($lease);
                $documentService->generateRecuEntreeAgence($lease);
            }

            // Log audit
            AuditService::log(
                'created',
                Lease::class,
                $lease->id,
                $data,
                (int) $lease->sci_id
            );

            return $lease;
        });
    }

    /**
     * Create a meuble (furnished short-stay) lease.
     */
    private function createMeubleLease(array $data, Property $property): Lease
    {
        $checkIn = \Carbon\Carbon::parse($data['check_in_date']);
        $checkOut = \Carbon\Carbon::parse($data['check_out_date']);
        $nights = $checkIn->diffInDays($checkOut);

        $dailyRate = (float) ($data['daily_rate'] ?? $property->daily_rate);

        // Progressive discount tiers
        $discountRate = match (true) {
            $nights >= 21 => 15,
            $nights >= 14 => 10,
            $nights >= 7  => 5,
            default       => 0,
        };

        $subtotal = $dailyRate * $nights;
        $discountAmount = round($subtotal * $discountRate / 100, 2);
        $totalAmount = $subtotal - $discountAmount;

        $leaseData = array_merge($data, [
            'lease_type'         => 'meuble',
            'start_date'         => $checkIn->toDateString(),
            'end_date'           => $checkOut->toDateString(),
            'check_in_date'      => $checkIn->toDateString(),
            'check_out_date'     => $checkOut->toDateString(),
            'nights_count'       => $nights,
            'daily_rate'         => $dailyRate,
            'discount_rate'      => $discountRate,
            'discount_amount'    => $discountAmount,
            'total_amount'       => $totalAmount,
            'rent_amount'        => $totalAmount,
            'deposit_amount'     => 0,
            'caution_2_mois'     => 0,
            'loyers_avances_2_mois' => 0,
            'frais_agence'       => 0,
            'penalty_rate'       => 0,
            'penalty_delay_days' => 0,
            'due_day'            => null,
        ]);

        $lease = Lease::create($leaseData);

        // Set property status to 'occupe'
        Property::where('id', $property->id)->update(['status' => 'occupe']);

        // If active, create a single LeaseMonthly for the total amount
        if ($lease->status === 'actif') {
            $this->monthlyService->generateForMeubleLease($lease);
        }

        // Log audit
        AuditService::log(
            'created',
            Lease::class,
            $lease->id,
            $leaseData,
            (int) $lease->sci_id
        );

        return $lease;
    }

    /**
     * Terminate a lease.
     *
     * Sets status to 'resilie', records termination date and reason,
     * sets the property back to 'disponible', and logs audit.
     */
    public function terminateLease(Lease $lease, array $data): Lease
    {
        return DB::transaction(function () use ($lease, $data): Lease {
            $terminationDate = $data['termination_date'] ?? now()->toDateString();

            $lease->update(array_merge($data, [
                'status'           => 'resilie',
                'termination_date' => $terminationDate,
            ]));

            // Delete future unpaid monthlies (months after termination with no payments)
            $terminationMonth = \Carbon\Carbon::parse($terminationDate)->format('Y-m');
            $lease->leaseMonthlies()
                ->where('month', '>', $terminationMonth)
                ->where('paid_amount', 0)
                ->delete();

            // Set property back to 'disponible'
            Property::where('id', $lease->property_id)->update(['status' => 'disponible']);

            // Log audit
            AuditService::log(
                'terminated',
                Lease::class,
                $lease->id,
                $data,
                (int) $lease->sci_id
            );

            return $lease->refresh();
        });
    }

    /**
     * Activate a lease that is currently 'en_attente'.
     *
     * Changes status to 'actif', generates monthly records,
     * and sets the property to 'occupe'.
     *
     * @throws \InvalidArgumentException If lease is not in 'en_attente' status.
     */
    public function activateLease(Lease $lease): Lease
    {
        if ($lease->status !== 'en_attente') {
            throw new \InvalidArgumentException(
                "Seul un bail en attente peut etre active. Statut actuel: {$lease->status}"
            );
        }

        return DB::transaction(function () use ($lease): Lease {
            $lease->update(['status' => 'actif']);

            // Set property to 'occupe'
            Property::where('id', $lease->property_id)->update(['status' => 'occupe']);

            // Generate monthlies
            if ($lease->isMeuble()) {
                $this->monthlyService->generateForMeubleLease($lease);
            } else {
                $this->monthlyService->generateForLease($lease);
            }

            // Log audit
            AuditService::log(
                'activated',
                Lease::class,
                $lease->id,
                ['status' => 'actif'],
                (int) $lease->sci_id
            );

            return $lease->refresh();
        });
    }
}
