<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LeaseMonthly;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyExcelViewController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', LeaseMonthly::class);

        $sciId = $request->attributes->get('sci_id');
        // Support separate month/year filters or legacy combined 'month' param
        if ($request->filled('filter_year') && $request->filled('filter_month')) {
            $month = $request->input('filter_year') . '-' . str_pad($request->input('filter_month'), 2, '0', STR_PAD_LEFT);
        } else {
            $month = $request->input('month', now()->format('Y-m'));
        }

        $query = LeaseMonthly::query()
            ->with([
                'lease.tenant',
                'lease.property',
                'payments' => fn ($q) => $q->orderByDesc('paid_at'),
            ])
            ->where('month', $month);

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        $monthlies = $query->orderBy('lease_id')->paginate(20)->withQueryString();

        return view('excel.monthly-management', compact('monthlies', 'month'));
    }
}
