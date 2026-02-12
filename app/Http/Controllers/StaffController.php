<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\StaffMember;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $sciId = $request->attributes->get('sci_id');
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $staffQuery = StaffMember::query()->orderBy('last_name')->orderBy('first_name');

        if ($sciId) {
            $staffQuery->where('sci_id', $sciId);
        }

        if ($request->input('status') === 'inactive') {
            $staffQuery->where('is_active', false);
        } else {
            $staffQuery->where('is_active', true);
        }

        $staff = $staffQuery->get();

        // Load payrolls for selected month/year
        $payrollQuery = Payroll::where('month', $month)->where('year', $year)->with('staffMember');
        if ($sciId) {
            $payrollQuery->where('sci_id', $sciId);
        }
        $payrolls = $payrollQuery->get()->keyBy('staff_member_id');

        $totalPayroll = (float) $payrolls->sum('amount');

        return view('staff.index', compact('staff', 'payrolls', 'month', 'year', 'totalPayroll'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sci_id'      => ['required', 'exists:scis,id'],
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'role'        => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'email'       => ['nullable', 'email', 'max:255'],
            'address'     => ['nullable', 'string', 'max:500'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date'   => ['nullable', 'date'],
        ], [
            'sci_id.required'     => 'Veuillez sélectionner une SCI.',
            'sci_id.exists'       => 'La SCI sélectionnée est invalide.',
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.email'         => 'L\'adresse email n\'est pas valide.',
            'base_salary.numeric' => 'Le salaire doit être un nombre.',
        ]);

        $member = StaffMember::create($data);

        AuditService::log('created', $member, $data);

        return redirect()->back()->with('success', 'Personnel ajouté avec succès.');
    }

    public function update(Request $request, StaffMember $staff): RedirectResponse
    {
        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'role'        => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'email'       => ['nullable', 'email', 'max:255'],
            'address'     => ['nullable', 'string', 'max:500'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date'   => ['nullable', 'date'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        $staff->update($data);

        AuditService::log('updated', $staff, $data);

        return redirect()->back()->with('success', 'Personnel mis à jour.');
    }

    public function destroy(StaffMember $staff): RedirectResponse
    {
        $staff->delete();

        AuditService::log('deleted', $staff);

        return redirect()->back()->with('success', 'Personnel supprimé.');
    }

    public function storePayroll(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'staff_member_id' => ['required', 'exists:staff_members,id'],
            'sci_id'          => ['required', 'exists:scis,id'],
            'month'           => ['required', 'integer', 'min:1', 'max:12'],
            'year'            => ['required', 'integer', 'min:2020'],
            'amount'          => ['required', 'numeric', 'min:0'],
            'paid_at'         => ['required', 'date'],
            'payment_method'  => ['required', 'in:especes,virement,cheque,mobile_money,versement_especes,depot_bancaire'],
            'reference'       => ['nullable', 'string', 'max:255'],
            'note'            => ['nullable', 'string'],
            'receipt'         => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'sci_id.required'          => 'Veuillez sélectionner une SCI.',
            'staff_member_id.required' => 'Veuillez sélectionner un membre du personnel.',
            'amount.required'          => 'Le montant est obligatoire.',
            'amount.numeric'           => 'Le montant doit être un nombre.',
            'paid_at.required'         => 'La date de paiement est obligatoire.',
            'payment_method.required'  => 'Le mode de paiement est obligatoire.',
            'receipt.mimes'            => 'Le justificatif doit être au format JPG, PNG ou PDF.',
            'receipt.max'              => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/payrolls', 'public');
        }
        unset($data['receipt']);

        $data['recorded_by'] = auth()->id();

        $payroll = Payroll::updateOrCreate(
            [
                'staff_member_id' => $data['staff_member_id'],
                'month' => $data['month'],
                'year' => $data['year'],
            ],
            collect($data)->except(['staff_member_id', 'month', 'year'])->toArray(),
        );

        AuditService::log('created', $payroll, $data);

        return redirect()->back()->with('success', 'Paie enregistrée avec succès.');
    }

    public function destroyPayroll(Payroll $payroll): RedirectResponse
    {
        $payroll->delete();

        AuditService::log('deleted', $payroll);

        return redirect()->back()->with('success', 'Paie supprimée.');
    }
}
