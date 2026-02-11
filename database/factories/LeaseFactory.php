<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Sci;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lease>
 */
class LeaseFactory extends Factory
{
    protected $model = Lease::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-12 months', '-3 months');
        $durationMonths = fake()->randomElement([12, 24, 36]);

        return [
            'sci_id' => Sci::factory(),
            'property_id' => Property::factory(),
            'tenant_id' => Tenant::factory(),
            'start_date' => $startDate,
            'end_date' => (clone $startDate)->modify("+{$durationMonths} months"),
            'duration_months' => $durationMonths,
            'rent_amount' => fake()->numberBetween(80, 400) * 1000,
            'charges_amount' => fake()->randomElement([0, 10000, 15000, 20000, 25000]),
            'deposit_amount' => fake()->numberBetween(100, 400) * 1000,
            'payment_method' => fake()->randomElement(['especes', 'mobile_money', 'virement', 'cheque']),
            'due_day' => fake()->randomElement([1, 5, 10]),
            'penalty_rate' => fake()->randomElement([0, 2, 5, 10]),
            'penalty_delay_days' => fake()->randomElement([0, 5, 10, 15]),
            'status' => 'actif',
            'termination_date' => null,
            'termination_reason' => null,
            'signed_lease_path' => null,
            'entry_inspection_path' => null,
            'exit_inspection_path' => null,
            'notes' => null,
        ];
    }

    /**
     * Indicate that the lease is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'actif',
        ]);
    }

    /**
     * Indicate that the lease is terminated.
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'resilie',
            'termination_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'termination_reason' => fake()->randomElement([
                'Non-paiement des loyers',
                'Résiliation à l\'amiable',
                'Fin de bail non renouvelé',
                'Départ du locataire',
            ]),
        ]);
    }

    /**
     * Indicate that the lease is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'en_attente',
        ]);
    }
}
