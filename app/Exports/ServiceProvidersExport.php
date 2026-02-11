<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceProvidersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $sciId = null,
    ) {}

    public function query(): Builder
    {
        $query = ServiceProvider::query();

        if ($this->sciId !== null) {
            $query->where('sci_id', $this->sciId);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Nom', 'Entreprise', 'Catégorie', 'Spécialité', 'Téléphone', 'Email', 'Statut'];
    }

    public function map($provider): array
    {
        return [
            $provider->name,
            $provider->company,
            $provider->category_label,
            $provider->specialty,
            $provider->phone,
            $provider->email,
            $provider->is_active ? 'Actif' : 'Inactif',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
