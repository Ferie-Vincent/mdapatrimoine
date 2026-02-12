@extends('layouts.app')

@section('title', 'ID DOSSIER - ' . ($lease->dossier_number ?? $lease->id))

@section('actions')
    <div class="flex items-center gap-1.5">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Imprimer
        </button>
        <a href="{{ route('leases.show', $lease) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm print:hidden">
            Fiche complète
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-lg shadow-lg border-2 border-brand-500 overflow-hidden">
            {{-- Header --}}
            <div class="bg-brand-600 text-white px-6 py-4 text-center">
                <h2 class="text-xl font-bold tracking-wide">APPT {{ $lease->property->numero_porte ?? $lease->dossier_number ?? $lease->id }}</h2>
                <p class="text-brand-200 text-sm mt-1">{{ $lease->sci->name ?? '' }}</p>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                <div class="text-center border-b border-gray-200 pb-4">
                    <p class="text-lg font-semibold text-gray-900">{{ $lease->tenant->full_name ?? '-' }}</p>
                    <p class="text-sm text-gray-500">{{ $lease->tenant->phone ?? '' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 font-medium">COMPTEUR CIE</p>
                        <p class="text-gray-900 font-semibold">{{ $lease->property->cie_meter_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">COMPTEUR SODECI</p>
                        <p class="text-gray-900 font-semibold">{{ $lease->property->sodeci_meter_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">BIEN</p>
                        <p class="text-gray-900">{{ $lease->property->reference ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">TYPE</p>
                        <p class="text-gray-900">{{ $lease->property->apartment_type_label ?? ucfirst($lease->property->type ?? '') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">ETAGE</p>
                        <p class="text-gray-900">{{ $lease->property->floor_label ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">ADRESSE</p>
                        <p class="text-gray-900">{{ $lease->property->address ?? '-' }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-green-50 rounded p-3">
                        <p class="text-green-700 font-medium">ENTREE</p>
                        <p class="text-green-900 font-semibold">{{ $lease->entry_inventory_date?->format('d/m/Y') ?? $lease->start_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div class="bg-red-50 rounded p-3">
                        <p class="text-red-700 font-medium">SORTIE</p>
                        <p class="text-red-900 font-semibold">{{ $lease->actual_exit_date?->format('d/m/Y') ?? $lease->exit_inventory_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">LOYER MENSUEL</span>
                        <span class="font-semibold text-gray-900">{{ number_format((float)$lease->rent_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if((float)($lease->deposit_amount ?? 0) > 0)
                        @php
                            $cautionDue = (float)($lease->deposit_amount ?? 0) - (float)($lease->deposit_returned_amount ?? 0);
                        @endphp
                        <div class="flex justify-between">
                            <span class="text-gray-500">CAUTION</span>
                            <span class="font-semibold text-gray-900">{{ number_format((float)$lease->deposit_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">CAUTION DUE</span>
                            @if($cautionDue <= 0)
                                <span class="font-semibold text-green-600">0 FCFA</span>
                            @else
                                <span class="font-semibold text-orange-600">{{ number_format($cautionDue, 0, ',', ' ') }} FCFA</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
