<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\LeaseMonthly;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthliesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $sciId = null,
        private readonly ?string $month = null,
        private readonly ?string $status = null,
    ) {}

    public function query(): Builder
    {
        $query = LeaseMonthly::query()->with('lease.tenant', 'lease.property');

        if ($this->sciId !== null) {
            $query->where('sci_id', $this->sciId);
        }
        if ($this->month !== null) {
            $query->where('month', $this->month);
        }
        if ($this->status !== null) {
            $query->where('status', $this->status);
        }

        return $query->orderByDesc('month');
    }

    public function headings(): array
    {
        return ['Mois', 'Locataire', 'Bien', 'Total dû', 'Payé', 'Reste', 'Statut'];
    }

    public function map($monthly): array
    {
        return [
            $monthly->month,
            $monthly->lease?->tenant?->full_name,
            $monthly->lease?->property?->reference,
            number_format((float) $monthly->total_due, 0, ',', ' '),
            number_format((float) $monthly->paid_amount, 0, ',', ' '),
            number_format((float) $monthly->remaining_amount, 0, ',', ' '),
            ucfirst($monthly->status),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
