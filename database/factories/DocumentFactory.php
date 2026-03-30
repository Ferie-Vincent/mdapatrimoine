<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'sci_id'       => null,
            'type'         => fake()->randomElement(['quittance', 'recu', 'avis', 'attestation', 'rapport']),
            'related_type' => null,
            'related_id'   => null,
            'month'        => now()->format('Y-m'),
            'path'         => 'documents/test/' . fake()->uuid() . '.pdf',
            'meta'         => null,
            'generated_by' => null,
        ];
    }
}
