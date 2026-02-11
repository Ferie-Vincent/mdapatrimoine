<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $sciId = null,
        private readonly ?string $fromMonth = null,
        private readonly ?string $toMonth = null,
    ) {}

    public function query(): Builder
    {
        $query = Payment::query()
            ->with('leaseMonthly.lease.tenant', 'leaseMonthly.lease.property');

        if ($this->sciId !== null) {
            $query->where('sci_id', $this->sciId);
        }

        if ($this->fromMonth !== null) {
            $query->where('paid_at', '>=', $this->fromMonth . '-01');
        }

        if ($this->toMonth !== null) {
            $query->where('paid_at', '<=', $this->toMonth . '-31');
        }

        return $query;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Date',
            'Montant',
            'Méthode',
            'Référence',
            'Locataire',
            'Bien',
            'Mois',
            'Note',
        ];
    }

    /**
     * @param  Payment  $payment
     * @return array<int, mixed>
     */
    public function map($payment): array
    {
        return [
            $payment->paid_at?->format('d/m/Y'),
            number_format((float) $payment->amount, 0, ',', ' '),
            $payment->method,
            $payment->reference,
            $payment->leaseMonthly?->lease?->tenant?->full_name,
            $payment->leaseMonthly?->lease?->property?->reference,
            $payment->leaseMonthly?->month,
            $payment->note,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
