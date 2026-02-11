<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Models\Sci;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lease_monthly_id' => LeaseMonthly::factory(),
            'sci_id' => Sci::factory(),
            'amount' => fake()->numberBetween(50, 400) * 1000,
            'paid_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'method' => fake()->randomElement(['especes', 'mobile_money', 'virement', 'cheque']),
            'reference' => strtoupper(fake()->bothify('PAY-####-??')),
            'note' => fake()->optional(0.3)->randomElement([
                'Paiement reçu en main propre',
                'Virement bancaire confirmé',
                'Paiement via Orange Money',
                'Paiement via Wave',
                'Chèque encaissé',
                'Paiement partiel - solde attendu',
            ]),
            'recorded_by' => null,
        ];
    }
}
