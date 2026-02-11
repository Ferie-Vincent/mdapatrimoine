<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        $sciId = $request->attributes->get('sci_id');

        $query = Property::query()->with('sci');

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $properties = $query->orderBy('reference')
            ->paginate(15)
            ->withQueryString();

        return view('properties.index', compact('properties'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('properties.index');
    }

    public function store(StorePropertyRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Property::class);

        $data = $request->validated();

        unset($data['photos']);

        $property = Property::create($data);

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $file) {
                $paths[] = $file->store("properties/{$property->id}/photos", 'public');
            }
            $property->update(['photos' => $paths]);
        }

        AuditService::log('created', $property, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Bien cree avec succes.']);
        }

        return redirect()
            ->route('properties.show', $property)
            ->with('success', 'Bien cree avec succes.');
    }

    public function show(Property $property): View
    {
        $this->authorize('view', $property);

        $property->load('sci');

        $activeLease = $property->leases()
            ->where('status', 'actif')
            ->with('tenant')
            ->first();

        $leaseHistory = $property->leases()
            ->with('tenant')
            ->orderByDesc('start_date')
            ->get();

        return view('properties.show', compact('property', 'activeLease', 'leaseHistory'));
    }

    public function edit(Property $property): RedirectResponse
    {
        return redirect()->route('properties.show', $property);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $property);

        $data = $request->validated();

        unset($data['photos'], $data['delete_photos']);

        $currentPhotos = $property->photos ?? [];

        // Remove photos marked for deletion
        $toDelete = $request->input('delete_photos', []);
        if (!empty($toDelete)) {
            foreach ($toDelete as $photo) {
                Storage::disk('public')->delete($photo);
            }
            $currentPhotos = array_values(array_diff($currentPhotos, $toDelete));
        }

        // Add new photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $currentPhotos[] = $file->store("properties/{$property->id}/photos", 'public');
            }
        }

        $data['photos'] = !empty($currentPhotos) ? array_values($currentPhotos) : null;

        $property->update($data);

        AuditService::log('updated', $property, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Bien mis a jour avec succes.']);
        }

        return redirect()
            ->route('properties.show', $property)
            ->with('success', 'Bien mis a jour avec succes.');
    }

    public function deletePhoto(Property $property, int $index): JsonResponse
    {
        $this->authorize('update', $property);

        $photos = $property->photos ?? [];

        if (!isset($photos[$index])) {
            return response()->json(['success' => false, 'message' => 'Photo introuvable.'], 404);
        }

        Storage::disk('public')->delete($photos[$index]);
        array_splice($photos, $index, 1);

        $property->update(['photos' => !empty($photos) ? $photos : null]);

        AuditService::log('updated', $property, ['action' => 'photo_deleted']);

        return response()->json(['success' => true, 'message' => 'Photo supprimee.']);
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        AuditService::log('deleted', $property);

        return redirect()
            ->route('properties.index')
            ->with('success', 'Bien supprime avec succes.');
    }
}
