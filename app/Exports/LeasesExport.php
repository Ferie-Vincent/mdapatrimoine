<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Lease;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeasesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $sciId = null,
    ) {}

    public function query(): Builder
    {
        $query = Lease::query()->with('property', 'tenant');

        if ($this->sciId !== null) {
            $query->where('sci_id', $this->sciId);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Bien', 'Locataire', 'Loyer', 'Charges', 'Début', 'Fin', 'Statut'];
    }

    public function map($lease): array
    {
        return [
            $lease->property?->reference,
            $lease->tenant?->full_name,
            number_format((float) $lease->rent_amount, 0, ',', ' '),
            number_format((float) $lease->charges_amount, 0, ',', ' '),
            $lease->start_date?->format('d/m/Y'),
            $lease->end_date?->format('d/m/Y'),
            ucfirst($lease->status),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
