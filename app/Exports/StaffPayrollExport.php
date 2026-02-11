<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Payroll;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffPayrollExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $sciId = null,
        private readonly ?int $month = null,
        private readonly ?int $year = null,
    ) {}

    public function query(): Builder
    {
        $query = Payroll::query()->with('staffMember');

        if ($this->sciId !== null) {
            $query->where('sci_id', $this->sciId);
        }
        if ($this->month !== null) {
            $query->where('month', $this->month);
        }
        if ($this->year !== null) {
            $query->where('year', $this->year);
        }

        return $query->orderByDesc('paid_at');
    }

    public function headings(): array
    {
        return ['Personnel', 'Montant', 'Date paiement', 'Mode', 'Référence'];
    }

    public function map($payroll): array
    {
        return [
            $payroll->staffMember?->full_name,
            number_format((float) $payroll->amount, 0, ',', ' '),
            $payroll->paid_at?->format('d/m/Y'),
            $payroll->payment_method,
            $payroll->reference,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
