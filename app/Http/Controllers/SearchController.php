<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q'));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $sciId = $request->attributes->get('sci_id');
        $like = '%' . $q . '%';
        $limit = 5;

        $results = [];

        // Tenants
        $tenants = Tenant::query()
            ->when($sciId, fn ($query) => $query->where('sci_id', $sciId))
            ->where(function ($query) use ($like) {
                $query->where('first_name', 'LIKE', $like)
                      ->orWhere('last_name', 'LIKE', $like)
                      ->orWhere('phone', 'LIKE', $like)
                      ->orWhere('email', 'LIKE', $like)
                      ->orWhere('id_number', 'LIKE', $like);
            })
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'phone']);

        foreach ($tenants as $t) {
            $results[] = [
                'type' => 'locataire',
                'icon' => 'user',
                'label' => $t->first_name . ' ' . $t->last_name,
                'sub' => $t->phone ?? '',
                'url' => route('tenants.show', $t->id),
            ];
        }

        // Properties
        $properties = Property::query()
            ->when($sciId, fn ($query) => $query->where('sci_id', $sciId))
            ->where(function ($query) use ($like) {
                $query->where('reference', 'LIKE', $like)
                      ->orWhere('address', 'LIKE', $like)
                      ->orWhere('city', 'LIKE', $like)
                      ->orWhere('description', 'LIKE', $like);
            })
            ->limit($limit)
            ->get(['id', 'reference', 'address', 'city']);

        foreach ($properties as $p) {
            $results[] = [
                'type' => 'bien',
                'icon' => 'building',
                'label' => $p->reference,
                'sub' => trim(($p->address ?? '') . ', ' . ($p->city ?? ''), ', '),
                'url' => route('properties.show', $p->id),
            ];
        }

        // Leases
        $leases = Lease::query()
            ->when($sciId, fn ($query) => $query->where('sci_id', $sciId))
            ->with('tenant:id,first_name,last_name')
            ->where(function ($query) use ($like) {
                $query->where('dossier_number', 'LIKE', $like)
                      ->orWhere('agency_name', 'LIKE', $like)
                      ->orWhereHas('tenant', function ($sub) use ($like) {
                          $sub->where('first_name', 'LIKE', $like)
                              ->orWhere('last_name', 'LIKE', $like);
                      });
            })
            ->limit($limit)
            ->get(['id', 'dossier_number', 'tenant_id', 'status']);

        foreach ($leases as $l) {
            $tenantName = $l->tenant ? $l->tenant->first_name . ' ' . $l->tenant->last_name : '';
            $results[] = [
                'type' => 'bail',
                'icon' => 'document',
                'label' => $l->dossier_number ?: 'Bail #' . $l->id,
                'sub' => $tenantName,
                'url' => route('leases.show', $l->id),
            ];
        }

        // Payments
        $payments = Payment::query()
            ->whereHas('leaseMonthly', function ($query) use ($sciId) {
                if ($sciId) {
                    $query->where('sci_id', $sciId);
                }
            })
            ->where(function ($query) use ($like) {
                $query->where('reference', 'LIKE', $like)
                      ->orWhere('note', 'LIKE', $like);
            })
            ->limit($limit)
            ->get(['id', 'reference', 'amount', 'paid_at']);

        foreach ($payments as $pay) {
            $results[] = [
                'type' => 'paiement',
                'icon' => 'cash',
                'label' => $pay->reference ?: 'Paiement #' . $pay->id,
                'sub' => number_format((float) $pay->amount, 0, ',', ' ') . ' F',
                'url' => route('payments.show', $pay->id),
            ];
        }

        return response()->json($results);
    }
}
