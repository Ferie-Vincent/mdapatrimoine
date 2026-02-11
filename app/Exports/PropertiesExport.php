<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PropertiesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $sciId = null,
    ) {}

    public function query(): Builder
    {
        $query = Property::query()->with('sci');

        if ($this->sciId !== null) {
            $query->where('sci_id', $this->sciId);
        }

        return $query;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Référence',
            'Type',
            'Adresse',
            'Ville',
            'SCI',
            'Statut',
            'Niveau',
            'N° Porte',
            'Surface',
        ];
    }

    /**
     * @param  Property  $property
     * @return array<int, mixed>
     */
    public function map($property): array
    {
        return [
            $property->reference,
            $property->type,
            $property->address,
            $property->city,
            $property->sci?->name,
            $property->status,
            $property->niveau,
            $property->numero_porte,
            $property->surface,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
