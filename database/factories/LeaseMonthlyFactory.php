<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Sci;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaseMonthly>
 */
class LeaseMonthlyFactory extends Factory
{
    protected $model = LeaseMonthly::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rentDue = fake()->numberBetween(80, 400) * 1000;
        $chargesDue = fake()->randomElement([0, 10000, 15000, 20000, 25000]);
        $penaltyDue = 0;
        $totalDue = $rentDue + $chargesDue + $penaltyDue;

        return [
            'lease_id' => Lease::factory(),
            'sci_id' => Sci::factory(),
            'month' => now()->format('Y-m'),
            'rent_due' => $rentDue,
            'charges_due' => $chargesDue,
            'penalty_due' => $penaltyDue,
            'total_due' => $totalDue,
            'paid_amount' => 0,
            'remaining_amount' => $totalDue,
            'status' => 'impaye',
            'due_date' => now()->startOfMonth()->addDays(4),
        ];
    }

    /**
     * Indicate that the monthly is fully paid.
     */
    public function paid(): static
    {
        return $this->state(function (array $attributes): array {
            $totalDue = $attributes['total_due'];

            return [
                'paid_amount' => $totalDue,
                'remaining_amount' => 0,
                'status' => 'paye',
            ];
        });
    }

    /**
     * Indicate that the monthly is partially paid.
     */
    public function partial(): static
    {
        return $this->state(function (array $attributes): array {
            $totalDue = $attributes['total_due'];
            $paidAmount = (int) round($totalDue * fake()->randomFloat(2, 0.3, 0.7));

            return [
                'paid_amount' => $paidAmount,
                'remaining_amount' => $totalDue - $paidAmount,
                'status' => 'partiel',
            ];
        });
    }

    /**
     * Indicate that the monthly is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'en_retard',
        ]);
    }
}
