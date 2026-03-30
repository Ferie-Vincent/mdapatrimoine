<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payroll;
use App\Models\StaffMember;
use Illuminate\Database\Eloquent\Collection;

class StaffService
{
    /**
     * Liste du personnel filtré par SCI et statut actif/inactif.
     */
    public function getStaffList(?int $sciId, bool $activeOnly = true): Collection
    {
        $query = StaffMember::query()->orderBy('last_name')->orderBy('first_name');

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        $query->where('is_active', $activeOnly);

        return $query->get();
    }

    /**
     * Fiches de paie pour un mois/année, indexées par staff_member_id.
     *
     * @return array{payrolls: Collection, totalPayroll: float}
     */
    public function getPayrollsForMonth(?int $sciId, int $month, int $year): array
    {
        $query = Payroll::where('month', $month)->where('year', $year)->with('staffMember');

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        $payrolls = $query->get()->keyBy('staff_member_id');
        $totalPayroll = (float) $payrolls->sum('amount');

        return compact('payrolls', 'totalPayroll');
    }

    public function createStaffMember(array $data): StaffMember
    {
        $member = StaffMember::create($data);

        AuditService::log('created', $member, $data);

        return $member;
    }

    public function updateStaffMember(StaffMember $staff, array $data): StaffMember
    {
        $staff->update($data);

        AuditService::log('updated', $staff, $data);

        return $staff;
    }

    public function deleteStaffMember(StaffMember $staff): void
    {
        $staff->delete();

        AuditService::log('deleted', $staff);
    }

    /**
     * Enregistrer ou mettre à jour une fiche de paie (upsert par staff+mois+année).
     */
    public function upsertPayroll(array $data): Payroll
    {
        $payroll = Payroll::updateOrCreate(
            [
                'staff_member_id' => $data['staff_member_id'],
                'month'           => $data['month'],
                'year'            => $data['year'],
            ],
            collect($data)->except(['staff_member_id', 'month', 'year'])->toArray(),
        );

        AuditService::log('created', $payroll, $data);

        return $payroll;
    }

    public function deletePayroll(Payroll $payroll): void
    {
        $payroll->delete();

        AuditService::log('deleted', $payroll);
    }
}
