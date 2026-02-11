<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Property;
use App\Models\Sci;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['appartement', 'maison', 'studio', 'bureau', 'commerce']);

        $addresses = [
            'Cocody Riviera 3, Rue J15, Villa 12, Abidjan',
            'Cocody Angré 7ème Tranche, Ilot 8, Lot 34, Abidjan',
            'Marcory Résidentiel, Rue Pasteur, Apt 4B, Abidjan',
            'Plateau, Avenue Franchet d\'Espèrey, Immeuble Nabil, Abidjan',
            'Cocody II Plateaux Vallon, Lot 1204, Abidjan',
            'Yopougon Sicogi, Rue K12, Porte 7, Abidjan',
            'Bingerville Cité Lumière, Villa 45, Abidjan',
            'Riviera Attoban, Ilot 15, Villa 3, Abidjan',
            'Cocody Danga, Rue des Mangoustans, Abidjan',
            'Treichville Avenue 12, Immeuble Bel-Air, Apt 2A, Abidjan',
            'Marcory Zone 4C, Rue du Marché, Bureau 5, Abidjan',
            'Cocody Riviera Faya, Résidence Les Palmiers, Apt 8, Abidjan',
        ];

        $surfaceMap = [
            'studio' => fake()->numberBetween(18, 35),
            'appartement' => fake()->numberBetween(45, 120),
            'maison' => fake()->numberBetween(80, 250),
            'bureau' => fake()->numberBetween(20, 80),
            'commerce' => fake()->numberBetween(30, 150),
        ];

        $roomsMap = [
            'studio' => 1,
            'appartement' => fake()->numberBetween(2, 5),
            'maison' => fake()->numberBetween(3, 7),
            'bureau' => fake()->numberBetween(1, 4),
            'commerce' => fake()->numberBetween(1, 3),
        ];

        return [
            'sci_id' => Sci::factory(),
            'reference' => strtoupper(fake()->unique()->bothify('PROP-??-####')),
            'type' => $type,
            'address' => fake()->randomElement($addresses),
            'city' => fake()->randomElement(['Abidjan', 'Bingerville', 'Grand-Bassam', 'Assinie']),
            'description' => $this->generateDescription($type),
            'surface' => $surfaceMap[$type],
            'rooms' => $roomsMap[$type],
            'status' => 'disponible',
            'niveau' => fake()->randomElement([null, 'RDC', '1er', '2e', '3e']),
            'numero_porte' => fake()->optional()->numerify('A##'),
            'nb_keys' => fake()->numberBetween(2, 5),
            'nb_clim' => fake()->numberBetween(0, 3),
            'photos' => null,
        ];
    }

    /**
     * Indicate that the property is occupied.
     */
    public function occupied(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'occupe',
        ]);
    }

    /**
     * Indicate that the property is under construction/renovation.
     */
    public function underWork(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'travaux',
        ]);
    }

    /**
     * Generate a realistic description for a property type.
     */
    private function generateDescription(string $type): string
    {
        $descriptions = [
            'studio' => 'Studio meublé avec coin cuisine équipée, salle d\'eau avec douche. Climatisation split. Gardiennage 24h.',
            'appartement' => 'Bel appartement lumineux avec salon-salle à manger, cuisine aménagée, balcon. Résidence sécurisée avec gardien et parking.',
            'maison' => 'Villa avec jardin arboré, garage, dépendance gardien. Eau courante et forage. Quartier résidentiel calme.',
            'bureau' => 'Bureau professionnel en open space, climatisé, câblage réseau. Accès ascenseur, parking sous-sol.',
            'commerce' => 'Local commercial en bordure de route principale, grande vitrine, arrière-boutique et sanitaires.',
        ];

        return $descriptions[$type] ?? 'Bien immobilier en bon état général.';
    }
}
