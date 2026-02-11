<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Sci;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScisExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function query(): Builder
    {
        return Sci::query()->withCount('properties');
    }

    public function headings(): array
    {
        return ['Nom', 'RCCM', 'IFU', 'Email', 'Téléphone', 'Nb biens', 'Statut'];
    }

    public function map($sci): array
    {
        return [
            $sci->name,
            $sci->rccm,
            $sci->ifu,
            $sci->email,
            $sci->phone,
            $sci->properties_count,
            $sci->is_active ? 'Actif' : 'Inactif',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
