<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\StaffMember;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $sciId = null,
    ) {}

    public function query(): Builder
    {
        $query = StaffMember::query();

        if ($this->sciId !== null) {
            $query->where('sci_id', $this->sciId);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Nom & Prénoms', 'Poste', 'Téléphone', 'Email', 'Salaire de base', 'Date embauche', 'Statut'];
    }

    public function map($staff): array
    {
        return [
            $staff->full_name,
            $staff->role,
            $staff->phone,
            $staff->email,
            number_format((float) $staff->base_salary, 0, ',', ' '),
            $staff->hire_date?->format('d/m/Y'),
            $staff->is_active ? 'Actif' : 'Inactif',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
