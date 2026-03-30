<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Services\FinancialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialCurrentController extends Controller
{
    public function __construct(
        private readonly FinancialService $financialService,
    ) {}

    public function index(Request $request): View
    {
        $sciId = $request->attributes->get('sci_id');
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $data = $this->financialService->getMonthlyOverview($sciId, $month, $year);

        return view('excel.financial-current', array_merge($data, compact('month', 'year')));
    }

    public function storeBudget(StoreBudgetRequest $request): RedirectResponse
    {
        $this->financialService->upsertBudget($request->validated());

        return redirect()->back()->with('success', 'Budget mensuel défini avec succès.');
    }
}
