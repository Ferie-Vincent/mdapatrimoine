<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        $sciId = $request->attributes->get('sci_id');

        $query = Property::query()->with('sci')->whereNotNull('photos')->where('photos', '!=', '[]');

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $properties = $query->orderBy('reference')
            ->paginate(12)
            ->withQueryString();

        $totalPhotos = $properties->getCollection()->sum(fn ($p) => count($p->photos ?? []));

        return view('gallery.index', compact('properties', 'totalPhotos'));
    }
}
