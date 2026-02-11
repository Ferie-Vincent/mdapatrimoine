<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TenantsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $sciId = null,
    ) {}

    public function query(): Builder
    {
        $query = Tenant::query()->with('sci');

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
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'SCI',
            'Profession',
            'Statut',
        ];
    }

    /**
     * @param  Tenant  $tenant
     * @return array<int, mixed>
     */
    public function map($tenant): array
    {
        return [
            $tenant->last_name,
            $tenant->first_name,
            $tenant->email,
            $tenant->phone,
            $tenant->sci?->name,
            $tenant->profession,
            $tenant->is_active ? 'Actif' : 'Inactif',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
