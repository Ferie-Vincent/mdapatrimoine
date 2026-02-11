<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSciRequest;
use App\Http\Requests\UpdateSciRequest;
use App\Models\Sci;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SciController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Sci::class);

        $query = Sci::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $scis = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('scis.index', compact('scis'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('scis.index');
    }

    public function store(StoreSciRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Sci::class);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $sci = Sci::create($data);

        AuditService::log('created', $sci, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'SCI creee avec succes.']);
        }

        return redirect()
            ->route('scis.show', $sci)
            ->with('success', 'SCI creee avec succes.');
    }

    public function show(Sci $sci): View
    {
        $this->authorize('view', $sci);

        $sci->loadCount([
            'properties',
            'tenants',
            'leases as active_leases_count' => function ($query) {
                $query->where('status', 'actif');
            },
        ]);

        $properties = $sci->properties()->orderBy('reference')->get();

        $leases = $sci->leases()
            ->where('status', 'actif')
            ->with(['property', 'tenant'])
            ->orderByDesc('start_date')
            ->get();

        return view('scis.show', compact('sci', 'properties', 'leases'));
    }

    public function edit(Sci $sci): RedirectResponse
    {
        return redirect()->route('scis.show', $sci);
    }

    public function update(UpdateSciRequest $request, Sci $sci): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $sci);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $sci->update($data);

        AuditService::log('updated', $sci, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'SCI mise a jour avec succes.']);
        }

        return redirect()
            ->route('scis.show', $sci)
            ->with('success', 'SCI mise a jour avec succes.');
    }

    public function destroy(Sci $sci): RedirectResponse
    {
        $this->authorize('delete', $sci);

        $sci->delete();

        AuditService::log('deleted', $sci);

        return redirect()
            ->route('scis.index')
            ->with('success', 'SCI supprimee avec succes.');
    }
}
