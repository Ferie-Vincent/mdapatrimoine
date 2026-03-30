<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition(): array
    {
        return [
            'sci_id'            => null,
            'lease_monthly_id'  => null,
            'channel'           => fake()->randomElement(['email', 'sms', 'whatsapp', 'courrier']),
            'message'           => fake()->sentence(),
            'status'            => 'brouillon',
            'level'             => fake()->randomElement([1, 2, 3]),
            'sent_at'           => null,
            'sent_by'           => null,
        ];
    }
}
