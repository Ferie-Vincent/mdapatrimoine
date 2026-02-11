<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Sci;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstNames = [
            'Amadou', 'Moussa', 'Ibrahim', 'Kouadio', 'Yao', 'Sékou',
            'Fatou', 'Awa', 'Mariam', 'Adjoua', 'Aïcha', 'Konan',
            'Jean-Baptiste', 'Marie-Claire', 'Franck', 'Sandrine',
            'Hervé', 'Patricia', 'Ousmane', 'Kadiatou',
        ];

        $lastNames = [
            'Koné', 'Traoré', 'Coulibaly', 'Diallo', 'Touré', 'Ouattara',
            'Bamba', 'Cissé', 'N\'Guessan', 'Kouassi', 'Yapi', 'Aka',
            'Koffi', 'Dosso', 'Dembélé', 'Sanogo', 'Bédié', 'Gbagbo',
            'Meïté', 'Konaté',
        ];

        $professions = [
            'Ingénieur informatique', 'Comptable', 'Médecin', 'Avocat',
            'Commerçant', 'Enseignant', 'Banquier', 'Pharmacien',
            'Architecte', 'Consultant', 'Fonctionnaire', 'Entrepreneur',
        ];

        $employers = [
            'Orange Côte d\'Ivoire', 'MTN CI', 'SODECI', 'CIE',
            'BNETD', 'Port Autonome d\'Abidjan', 'CNPS',
            'Ministère de l\'Éducation', 'CHU de Cocody', 'SGBCI',
            'Total Energies CI', 'Bolloré Transport', 'Cargill CI',
            null, null,
        ];

        $firstName = fake()->randomElement($firstNames);
        $lastName = fake()->randomElement($lastNames);

        return [
            'sci_id' => Sci::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower(str_replace(['\'', ' '], '', $firstName)) . '.' . strtolower(str_replace(['\'', ' '], '', $lastName)) . '@' . fake()->randomElement(['gmail.com', 'yahoo.fr', 'hotmail.com', 'outlook.fr']),
            'phone' => '+225 ' . fake()->randomElement(['01', '05', '07']) . ' ' . fake()->numerify('## ## ##'),
            'phone_secondary' => fake()->optional(0.4)->passthrough('+225 ' . fake()->randomElement(['01', '05', '07']) . ' ' . fake()->numerify('## ## ##')),
            'address' => fake()->randomElement([
                'Cocody Riviera 2, Abidjan',
                'Yopougon Selmer, Abidjan',
                'Abobo Baoulé, Abidjan',
                'Marcory Sans Fil, Abidjan',
                'Treichville, Avenue 10, Abidjan',
                'Koumassi Remblais, Abidjan',
            ]),
            'id_type' => fake()->randomElement(['CNI', 'Passeport', 'Carte de séjour', 'Permis de conduire']),
            'id_number' => 'CI-' . fake()->numerify('########'),
            'id_expiration' => fake()->dateTimeBetween('+6 months', '+5 years'),
            'id_file_path' => null,
            'profession' => fake()->randomElement($professions),
            'employer' => fake()->randomElement($employers),
            'emergency_contact_name' => fake()->randomElement($firstNames) . ' ' . fake()->randomElement($lastNames),
            'emergency_contact_phone' => '+225 ' . fake()->randomElement(['01', '05', '07']) . ' ' . fake()->numerify('## ## ##'),
            'guarantor_name' => fake()->optional(0.6)->passthrough(fake()->randomElement($firstNames) . ' ' . fake()->randomElement($lastNames)),
            'guarantor_phone' => fake()->optional(0.6)->passthrough('+225 ' . fake()->randomElement(['01', '05', '07']) . ' ' . fake()->numerify('## ## ##')),
            'guarantor_address' => fake()->optional(0.4)->passthrough('Cocody Angré, Abidjan'),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the tenant is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
