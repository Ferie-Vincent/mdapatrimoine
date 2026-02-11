<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Sci;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── 1. Users ───────────────────────────────────────────────────────
        $admin = User::factory()->create([
            'name' => 'Administrateur SCIManager',
            'email' => 'admin@scimanager.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $gestionnaire = User::factory()->create([
            'name' => 'Kouadio Jean-Marc',
            'email' => 'gestionnaire@scimanager.com',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'is_active' => true,
        ]);

        $lecteur = User::factory()->create([
            'name' => 'Traoré Aminata',
            'email' => 'lecteur@scimanager.com',
            'password' => Hash::make('password'),
            'role' => 'lecture_seule',
            'is_active' => true,
        ]);

        // ─── 2. SCIs ────────────────────────────────────────────────────────
        $sci1 = Sci::factory()->create([
            'name' => 'SCI Ivoire Patrimoine',
            'rccm' => 'CI-ABJ-2019-B-14523',
            'ifu' => '1845327AB',
            'address' => 'Plateau, Rue du Commerce, Immeuble Alpha 2000, 3ème étage, Abidjan',
            'phone' => '+225 27 20 31 45 67',
            'email' => 'contact@ivoirepatrimoine.ci',
            'bank_name' => 'SGBCI',
            'bank_iban' => 'CI93 0100 0100 1234 5678 9012 345',
        ]);

        $sci2 = Sci::factory()->create([
            'name' => 'SCI Les Cocotiers',
            'rccm' => 'CI-ABJ-2021-B-28917',
            'ifu' => '2937481CD',
            'address' => 'Cocody Riviera Faya, Lot 245, Abidjan',
            'phone' => '+225 01 02 03 04 05',
            'email' => 'info@lescocotiers-sci.ci',
            'bank_name' => 'Ecobank CI',
            'bank_iban' => 'CI93 0050 0200 9876 5432 1098 765',
        ]);

        // ─── 3. Assign gestionnaire & lecteur to first SCI ──────────────────
        $sci1->users()->attach([$gestionnaire->id, $lecteur->id]);

        // ─── 4. Properties ──────────────────────────────────────────────────
        // SCI 1 - 4 properties
        $propsSci1 = [
            Property::factory()->create([
                'sci_id' => $sci1->id,
                'reference' => 'PROP-IP-0001',
                'type' => 'appartement',
                'address' => 'Cocody Riviera 3, Rue J15, Résidence Le Fromager, Apt 4B, Abidjan',
                'city' => 'Abidjan',
                'surface' => 85.00,
                'rooms' => 3,
                'status' => 'disponible',
                'rent_reference' => 250000,
                'charges_reference' => 25000,
                'nb_keys' => 3,
                'nb_clim' => 2,
            ]),
            Property::factory()->create([
                'sci_id' => $sci1->id,
                'reference' => 'PROP-IP-0002',
                'type' => 'maison',
                'address' => 'Cocody Angré 8ème Tranche, Ilot 12, Villa 7, Abidjan',
                'city' => 'Abidjan',
                'surface' => 180.00,
                'rooms' => 5,
                'status' => 'disponible',
                'rent_reference' => 450000,
                'charges_reference' => 30000,
                'nb_keys' => 4,
                'nb_clim' => 3,
            ]),
            Property::factory()->create([
                'sci_id' => $sci1->id,
                'reference' => 'PROP-IP-0003',
                'type' => 'studio',
                'address' => 'Marcory Résidentiel, Immeuble Bel-Air, Studio 2, Abidjan',
                'city' => 'Abidjan',
                'surface' => 28.00,
                'rooms' => 1,
                'status' => 'disponible',
                'rent_reference' => 85000,
                'charges_reference' => 10000,
                'nb_keys' => 2,
                'nb_clim' => 1,
            ]),
            Property::factory()->create([
                'sci_id' => $sci1->id,
                'reference' => 'PROP-IP-0004',
                'type' => 'bureau',
                'address' => 'Plateau, Avenue Noguès, Immeuble CCIA, Bureau 305, Abidjan',
                'city' => 'Abidjan',
                'surface' => 55.00,
                'rooms' => 2,
                'status' => 'disponible',
                'rent_reference' => 350000,
                'charges_reference' => 20000,
                'nb_keys' => 3,
                'nb_clim' => 2,
            ]),
        ];

        // SCI 2 - 3 properties
        $propsSci2 = [
            Property::factory()->create([
                'sci_id' => $sci2->id,
                'reference' => 'PROP-LC-0001',
                'type' => 'appartement',
                'address' => 'Cocody II Plateaux Vallon, Résidence Les Palmiers, Apt 12, Abidjan',
                'city' => 'Abidjan',
                'surface' => 95.00,
                'rooms' => 4,
                'status' => 'disponible',
                'rent_reference' => 300000,
                'charges_reference' => 25000,
                'nb_keys' => 3,
                'nb_clim' => 3,
            ]),
            Property::factory()->create([
                'sci_id' => $sci2->id,
                'reference' => 'PROP-LC-0002',
                'type' => 'maison',
                'address' => 'Bingerville, Cité des Cadres, Villa 34, Abidjan',
                'city' => 'Bingerville',
                'surface' => 200.00,
                'rooms' => 6,
                'status' => 'disponible',
                'rent_reference' => 500000,
                'charges_reference' => 35000,
                'nb_keys' => 5,
                'nb_clim' => 4,
            ]),
            Property::factory()->create([
                'sci_id' => $sci2->id,
                'reference' => 'PROP-LC-0003',
                'type' => 'commerce',
                'address' => 'Marcory Zone 4C, Boulevard VGE, Local 8, Abidjan',
                'city' => 'Abidjan',
                'surface' => 65.00,
                'rooms' => 2,
                'status' => 'disponible',
                'rent_reference' => 200000,
                'charges_reference' => 15000,
                'nb_keys' => 2,
                'nb_clim' => 1,
            ]),
        ];

        // ─── 5. Tenants ─────────────────────────────────────────────────────
        // SCI 1 - 3 tenants
        $tenantsSci1 = [
            Tenant::factory()->create([
                'sci_id' => $sci1->id,
                'first_name' => 'Amadou',
                'last_name' => 'Koné',
                'email' => 'amadou.kone@gmail.com',
                'phone' => '+225 07 08 45 12 34',
                'profession' => 'Ingénieur informatique',
                'employer' => 'Orange Côte d\'Ivoire',
            ]),
            Tenant::factory()->create([
                'sci_id' => $sci1->id,
                'first_name' => 'Fatou',
                'last_name' => 'Traoré',
                'email' => 'fatou.traore@yahoo.fr',
                'phone' => '+225 05 12 67 89 01',
                'profession' => 'Comptable',
                'employer' => 'SGBCI',
            ]),
            Tenant::factory()->create([
                'sci_id' => $sci1->id,
                'first_name' => 'Jean-Baptiste',
                'last_name' => 'N\'Guessan',
                'email' => 'jb.nguessan@outlook.fr',
                'phone' => '+225 01 23 45 67 89',
                'profession' => 'Avocat',
                'employer' => null,
            ]),
        ];

        // SCI 2 - 2 tenants
        $tenantsSci2 = [
            Tenant::factory()->create([
                'sci_id' => $sci2->id,
                'first_name' => 'Sékou',
                'last_name' => 'Coulibaly',
                'email' => 'sekou.coulibaly@gmail.com',
                'phone' => '+225 07 98 76 54 32',
                'profession' => 'Médecin',
                'employer' => 'CHU de Cocody',
            ]),
            Tenant::factory()->create([
                'sci_id' => $sci2->id,
                'first_name' => 'Marie-Claire',
                'last_name' => 'Kouassi',
                'email' => 'mc.kouassi@hotmail.com',
                'phone' => '+225 05 45 67 89 01',
                'profession' => 'Commerçant',
                'employer' => null,
            ]),
        ];

        // ─── 6. Leases (active) ─────────────────────────────────────────────
        // SCI 1: 3 leases (tenant -> property)
        $leasesSci1 = [
            $this->createLease($sci1, $propsSci1[0], $tenantsSci1[0], 250000, 25000, '2025-06-01'),
            $this->createLease($sci1, $propsSci1[1], $tenantsSci1[1], 450000, 30000, '2025-08-01'),
            $this->createLease($sci1, $propsSci1[2], $tenantsSci1[2], 85000, 10000, '2025-10-01'),
        ];

        // SCI 2: 2 leases
        $leasesSci2 = [
            $this->createLease($sci2, $propsSci2[0], $tenantsSci2[0], 300000, 25000, '2025-07-01'),
            $this->createLease($sci2, $propsSci2[2], $tenantsSci2[1], 200000, 15000, '2025-09-01'),
        ];

        // ─── 7. Generate monthlies for last 3 months + current ──────────────
        $allLeases = array_merge($leasesSci1, $leasesSci2);

        foreach ($allLeases as $lease) {
            $this->generateMonthlies($lease, $gestionnaire);
        }
    }

    /**
     * Create a lease and mark the property as occupied.
     */
    private function createLease(Sci $sci, Property $property, Tenant $tenant, int $rent, int $charges, string $startDate): Lease
    {
        $property->update(['status' => 'occupe']);

        return Lease::factory()->create([
            'sci_id' => $sci->id,
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'start_date' => $startDate,
            'end_date' => Carbon::parse($startDate)->addMonths(24)->format('Y-m-d'),
            'duration_months' => 24,
            'rent_amount' => $rent,
            'charges_amount' => $charges,
            'deposit_amount' => $rent * 2,
            'payment_method' => fake()->randomElement(['especes', 'mobile_money', 'virement']),
            'due_day' => 5,
            'penalty_rate' => 5,
            'penalty_delay_days' => 10,
            'status' => 'actif',
        ]);
    }

    /**
     * Generate monthly records for a lease: last 3 months + current month.
     * Apply a realistic mix of paid, partial, and unpaid statuses.
     */
    private function generateMonthlies(Lease $lease, User $recordedBy): void
    {
        $now = Carbon::now();
        $months = [];

        // Last 3 months + current
        for ($i = 3; $i >= 0; $i--) {
            $months[] = $now->copy()->subMonths($i);
        }

        foreach ($months as $index => $monthDate) {
            // Skip months before lease start
            if ($monthDate->lt(Carbon::parse($lease->start_date)->startOfMonth())) {
                continue;
            }

            $monthStr = $monthDate->format('Y-m');
            $rentDue = (float) $lease->rent_amount;
            $chargesDue = (float) $lease->charges_amount;
            $penaltyDue = 0;
            $totalDue = $rentDue + $chargesDue + $penaltyDue;

            $dueDate = Carbon::createFromFormat('Y-m-d', $monthDate->format('Y-m') . '-' . str_pad((string) $lease->due_day, 2, '0', STR_PAD_LEFT));

            // Determine payment status based on month position:
            // Oldest months: paid, middle: mix, current: mostly unpaid
            if ($index === 0) {
                // 3 months ago: fully paid
                $paidAmount = $totalDue;
                $status = 'paye';
            } elseif ($index === 1) {
                // 2 months ago: paid or partial (70% chance paid)
                if (fake()->boolean(70)) {
                    $paidAmount = $totalDue;
                    $status = 'paye';
                } else {
                    $paidAmount = round($totalDue * 0.5);
                    $status = 'partiel';
                }
            } elseif ($index === 2) {
                // 1 month ago: partial or unpaid
                $roll = fake()->numberBetween(1, 100);
                if ($roll <= 40) {
                    $paidAmount = $totalDue;
                    $status = 'paye';
                } elseif ($roll <= 70) {
                    $paidAmount = round($totalDue * fake()->randomFloat(2, 0.3, 0.7));
                    $status = 'partiel';
                } else {
                    $paidAmount = 0;
                    $status = 'en_retard';
                    $penaltyDue = round($rentDue * ($lease->penalty_rate / 100));
                    $totalDue = $rentDue + $chargesDue + $penaltyDue;
                }
            } else {
                // Current month: mostly unpaid
                $roll = fake()->numberBetween(1, 100);
                if ($roll <= 20) {
                    $paidAmount = $totalDue;
                    $status = 'paye';
                } elseif ($roll <= 40) {
                    $paidAmount = round($totalDue * fake()->randomFloat(2, 0.2, 0.5));
                    $status = 'partiel';
                } else {
                    $paidAmount = 0;
                    $status = 'impaye';
                }
            }

            $remainingAmount = $totalDue - $paidAmount;

            $monthly = LeaseMonthly::factory()->create([
                'lease_id' => $lease->id,
                'sci_id' => $lease->sci_id,
                'month' => $monthStr,
                'rent_due' => $rentDue,
                'charges_due' => $chargesDue,
                'penalty_due' => $penaltyDue,
                'total_due' => $totalDue,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'status' => $status,
                'due_date' => $dueDate,
            ]);

            // Create payment records for paid amounts
            if ($paidAmount > 0) {
                $this->createPayments($monthly, (float) $paidAmount, $recordedBy, $monthDate);
            }
        }
    }

    /**
     * Create payment records for a monthly.
     * Sometimes split into 2 payments to simulate real-world partial payments.
     */
    private function createPayments(LeaseMonthly $monthly, float $totalPaid, User $recordedBy, Carbon $monthDate): void
    {
        $methods = ['especes', 'mobile_money', 'virement', 'cheque'];
        $shouldSplit = $monthly->status === 'partiel' || fake()->boolean(20);

        if ($shouldSplit && $totalPaid > 50000) {
            // Split into 2 payments
            $firstPayment = round($totalPaid * fake()->randomFloat(2, 0.4, 0.7));
            $secondPayment = $totalPaid - $firstPayment;

            Payment::factory()->create([
                'lease_monthly_id' => $monthly->id,
                'sci_id' => $monthly->sci_id,
                'amount' => $firstPayment,
                'paid_at' => $monthDate->copy()->addDays(fake()->numberBetween(3, 8)),
                'method' => fake()->randomElement($methods),
                'reference' => strtoupper('PAY-' . $monthly->month . '-' . fake()->bothify('####')),
                'note' => 'Premier versement',
                'recorded_by' => $recordedBy->id,
            ]);

            Payment::factory()->create([
                'lease_monthly_id' => $monthly->id,
                'sci_id' => $monthly->sci_id,
                'amount' => $secondPayment,
                'paid_at' => $monthDate->copy()->addDays(fake()->numberBetween(12, 25)),
                'method' => fake()->randomElement($methods),
                'reference' => strtoupper('PAY-' . $monthly->month . '-' . fake()->bothify('####')),
                'note' => 'Complément de paiement',
                'recorded_by' => $recordedBy->id,
            ]);
        } else {
            // Single payment
            Payment::factory()->create([
                'lease_monthly_id' => $monthly->id,
                'sci_id' => $monthly->sci_id,
                'amount' => $totalPaid,
                'paid_at' => $monthDate->copy()->addDays(fake()->numberBetween(1, 15)),
                'method' => fake()->randomElement($methods),
                'reference' => strtoupper('PAY-' . $monthly->month . '-' . fake()->bothify('####')),
                'note' => null,
                'recorded_by' => $recordedBy->id,
            ]);
        }
    }
}
