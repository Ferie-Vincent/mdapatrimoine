<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExcelDatabaseController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lease::class);

        $sciId = $request->attributes->get('sci_id');

        $query = Lease::query()
            ->with(['tenant', 'property', 'sci'])
            ->where('status', 'actif');

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('dossier_number', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('property', function ($pq) use ($search) {
                      $pq->where('reference', 'like', "%{$search}%");
                  });
            });
        }

        $leases = $query->orderBy('dossier_number')
            ->paginate(12)
            ->withQueryString();

        return view('excel.database', compact('leases'));
    }
}
