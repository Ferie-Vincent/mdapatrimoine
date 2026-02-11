@extends('layouts.app')

@section('title', $sci->name)

@section('actions')
    <div class="flex items-center gap-1.5">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimer
        </button>
        <button @click="$dispatch('open-modal', 'edit-sci-{{ $sci->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 rounded-lg text-xs font-semibold text-white hover:bg-amber-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modifier
        </button>
        <form method="POST" action="{{ route('scis.destroy', $sci) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette SCI ? Cette action est irréversible.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-accent-red-400 rounded-lg text-xs font-semibold text-white hover:bg-accent-red-500 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Supprimer
            </button>
        </form>
    </div>
@endsection

@section('content')
    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-stat-card title="Biens immobiliers" :value="(string) ($sci->properties_count ?? $properties->count())" icon="building" color="blue" />
        <x-stat-card title="Locataires" :value="(string) ($sci->tenants_count ?? 0)" icon="users" color="green" />
        <x-stat-card title="Baux actifs" :value="(string) ($sci->active_leases_count ?? $leases->count())" icon="document" color="brand" />
    </div>

    {{-- Detail Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 mb-6 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-slate-800 to-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($sci->logo_path)
                    <img class="h-12 w-12 rounded-xl object-cover ring-2 ring-white/20" src="{{ Storage::url($sci->logo_path) }}" alt="{{ $sci->name }}">
                @else
                    <div class="h-12 w-12 rounded-xl bg-white/15 flex items-center justify-center">
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($sci->name, 0, 2)) }}</span>
                    </div>
                @endif
                <div>
                    <h3 class="text-lg font-semibold text-white">{{ $sci->name }}</h3>
                    <p class="text-sm text-slate-300">{{ $sci->address ?? '' }}</p>
                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ring-1 {{ $sci->is_active ? 'bg-emerald-400/20 text-emerald-300 ring-emerald-400/30' : 'bg-red-400/20 text-red-300 ring-red-400/30' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $sci->is_active ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                {{ $sci->is_active ? 'Actif' : 'Inactif' }}
            </span>
        </div>
        <div class="px-6 py-5 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Informations legales</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">RCCM</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $sci->rccm ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">IFU</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $sci->ifu ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 py-5 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Contact</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Email</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $sci->email ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Telephone</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $sci->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @if($sci->bank_name || $sci->bank_iban)
        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Informations bancaires</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Banque</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $sci->bank_name ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">IBAN</p>
                        <p class="text-sm font-semibold text-gray-900 font-mono text-xs">{{ $sci->bank_iban ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Properties Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900">Biens de cette SCI</h3>
        </div>

        @if($properties->count())
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Référence</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Adresse</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-right text-sm font-medium text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($properties as $property)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900">{{ $property->reference }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($property->type) }}</td>
                            <td class="px-6 py-5 text-sm text-gray-500">{{ $property->address }}, {{ $property->city }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($property->status === 'disponible')
                                    <x-badge type="success">Disponible</x-badge>
                                @elseif($property->status === 'occupe')
                                    <x-badge type="info">Occupé</x-badge>
                                @elseif($property->status === 'travaux')
                                    <x-badge type="warning">Travaux</x-badge>
                                @else
                                    <x-badge type="default">{{ ucfirst($property->status) }}</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('properties.show', $property) }}" class="text-brand-600 hover:text-brand-900">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-8 text-center text-sm text-gray-500">Aucun bien pour cette SCI.</div>
        @endif
    </div>

    {{-- Active Leases --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900">Baux actifs</h3>
        </div>

        @if($leases->count())
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Bien</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Locataire</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Loyer</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Début</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Fin</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-400">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($leases as $lease)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-900">{{ $lease->property->reference ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">{{ $lease->tenant->full_name ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">{{ number_format($lease->rent_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">{{ $lease->start_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">{{ $lease->end_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <x-badge type="success">{{ ucfirst($lease->status) }}</x-badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-8 text-center text-sm text-gray-500">Aucun bail actif pour cette SCI.</div>
        @endif
    </div>

    {{-- Edit SCI Modal --}}
    <x-form-modal name="edit-sci-{{ $sci->id }}" title="Modifier {{ $sci->name }}" :action="route('scis.update', $sci)" method="PUT" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M3.75 3v18m16.5-18v18M5.25 6h.008v.008H5.25V6zm0 3h.008v.008H5.25V9zm0 3h.008v.008H5.25V12zm6-6h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12zm6-6h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12z"/></svg>' iconColor="text-amber-500">
        @include('scis._form', ['sci' => $sci])
    </x-form-modal>
@endsection
