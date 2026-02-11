<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LeaseMonthly;
use App\Models\Property;
use App\Models\Sci;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $userScis = $user->isSuperAdmin()
            ? Sci::where('is_active', true)->get()
            : $user->scis()->where('is_active', true)->get();

        if ($userScis->count() <= 1 && !$user->isSuperAdmin()) {
            abort(403);
        }

        $sciIds = $userScis->pluck('id');

        // Grouped financial data per SCI
        $financials = LeaseMonthly::whereIn('sci_id', $sciIds)
            ->selectRaw('sci_id, SUM(total_due) as expected, SUM(paid_amount) as collected, SUM(remaining_amount) as unpaid')
            ->groupBy('sci_id')
            ->get()
            ->keyBy('sci_id');

        // Property counts per SCI
        $propertyCounts = Property::whereIn('sci_id', $sciIds)
            ->selectRaw('sci_id, COUNT(*) as total, SUM(CASE WHEN status = "occupe" THEN 1 ELSE 0 END) as occupied')
            ->groupBy('sci_id')
            ->get()
            ->keyBy('sci_id');

        $comparison = $userScis->map(function (Sci $sci) use ($financials, $propertyCounts) {
            $fin = $financials->get($sci->id);
            $prop = $propertyCounts->get($sci->id);

            $expected = (float) ($fin->expected ?? 0);
            $collected = (float) ($fin->collected ?? 0);
            $unpaid = (float) ($fin->unpaid ?? 0);
            $total = (int) ($prop->total ?? 0);
            $occupied = (int) ($prop->occupied ?? 0);

            return [
                'name' => $sci->name,
                'expected' => $expected,
                'collected' => $collected,
                'unpaid' => $unpaid,
                'recovery_rate' => $expected > 0 ? round(($collected / $expected) * 100, 1) : 0,
                'properties' => $total,
                'occupied' => $occupied,
                'occupancy_rate' => $total > 0 ? round(($occupied / $total) * 100, 1) : 0,
            ];
        });

        return view('analytics.index', compact('comparison'));
    }
}
