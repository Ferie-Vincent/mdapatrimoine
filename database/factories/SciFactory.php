<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Sci;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sci>
 */
class SciFactory extends Factory
{
    protected $model = Sci::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sciNames = [
            'SCI Ivoire Patrimoine',
            'SCI Les Cocotiers',
            'SCI Lagune Immobilier',
            'SCI Plateau Résidences',
            'SCI Abidjan Invest',
            'SCI Bingerville Habitat',
            'SCI Riviera Prestige',
            'SCI Marcory Développement',
        ];

        $banks = [
            'SGBCI', 'BICICI', 'Ecobank CI', 'BACI', 'BOA Côte d\'Ivoire',
            'SIB', 'Coris Bank CI', 'Bridge Bank CI', 'NSIA Banque',
        ];

        return [
            'name' => fake()->unique()->randomElement($sciNames),
            'rccm' => 'CI-ABJ-' . fake()->year() . '-B-' . fake()->numerify('#####'),
            'ifu' => fake()->numerify('#######') . fake()->randomLetter() . fake()->randomLetter(),
            'address' => fake()->randomElement([
                'Plateau, Rue du Commerce, Immeuble Alpha 2000, Abidjan',
                'Cocody Riviera Faya, Lot 245, Abidjan',
                'Marcory Zone 4, Boulevard VGE, Abidjan',
                'Cocody Angré 8ème Tranche, Abidjan',
                'Yopougon Maroc, Rue des Jardins, Abidjan',
            ]),
            'phone' => '+225 ' . fake()->randomElement(['01', '05', '07']) . ' ' . fake()->numerify('## ## ##'),
            'email' => fake()->unique()->companyEmail(),
            'bank_name' => fake()->randomElement($banks),
            'bank_iban' => 'CI' . fake()->numerify('## #### #### #### #### #### ###'),
            'logo_path' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the SCI is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
