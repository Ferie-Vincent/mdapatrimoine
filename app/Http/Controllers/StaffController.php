<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\StoreStaffMemberRequest;
use App\Http\Requests\UpdateStaffMemberRequest;
use App\Models\Payroll;
use App\Models\StaffMember;
use App\Services\StaffService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(
        private readonly StaffService $staffService,
    ) {}

    public function index(Request $request): View
    {
        $sciId = $request->attributes->get('sci_id');
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $activeOnly = $request->input('status') !== 'inactive';
        $staff = $this->staffService->getStaffList($sciId, $activeOnly);

        ['payrolls' => $payrolls, 'totalPayroll' => $totalPayroll] = $this->staffService->getPayrollsForMonth($sciId, $month, $year);

        return view('staff.index', compact('staff', 'payrolls', 'month', 'year', 'totalPayroll'));
    }

    public function store(StoreStaffMemberRequest $request): RedirectResponse
    {
        $this->staffService->createStaffMember($request->validated());

        return redirect()->back()->with('success', 'Personnel ajouté avec succès.');
    }

    public function update(UpdateStaffMemberRequest $request, StaffMember $staff): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $this->staffService->updateStaffMember($staff, $data);

        return redirect()->back()->with('success', 'Personnel mis à jour.');
    }

    public function destroy(StaffMember $staff): RedirectResponse|JsonResponse
    {
        $this->staffService->deleteStaffMember($staff);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Personnel supprime avec succes.']);
        }

        return redirect()->back()->with('success', 'Personnel supprimé.');
    }

    public function storePayroll(StorePayrollRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/payrolls', 'public');
        }
        unset($data['receipt']);

        $data['recorded_by'] = auth()->id();

        $this->staffService->upsertPayroll($data);

        return redirect()->back()->with('success', 'Paie enregistrée avec succès.');
    }

    public function destroyPayroll(Payroll $payroll): RedirectResponse|JsonResponse
    {
        $this->staffService->deletePayroll($payroll);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Paie supprimee avec succes.']);
        }

        return redirect()->back()->with('success', 'Paie supprimée.');
    }
}
