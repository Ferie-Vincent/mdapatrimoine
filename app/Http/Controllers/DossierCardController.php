<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Lease;
use Illuminate\View\View;

class DossierCardController extends Controller
{
    public function show(Lease $lease): View
    {
        $this->authorize('view', $lease);

        $lease->load(['tenant', 'property', 'sci']);

        return view('excel.dossier-card', compact('lease'));
    }
}
