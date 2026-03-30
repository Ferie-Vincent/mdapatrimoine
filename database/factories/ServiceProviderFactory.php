<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceProvider>
 */
class ServiceProviderFactory extends Factory
{
    protected $model = ServiceProvider::class;

    public function definition(): array
    {
        return [
            'sci_id'    => null,
            'name'      => fake()->name(),
            'phone'     => '+225 07 ' . fake()->numerify('## ## ##'),
            'category'  => fake()->randomElement(['artisan', 'plombier', 'electricien', 'peintre', 'menuisier']),
            'specialty' => fake()->optional()->word(),
            'company'   => fake()->optional()->company(),
            'is_active' => true,
        ];
    }
}
