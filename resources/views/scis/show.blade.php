@extends('layouts.app')

@section('title', $sci->name)

@section('content')
    {{-- Hero Header --}}
    <div class="relative bg-gradient-to-br from-brand-700 via-brand-500 to-indigo-500 dark:from-brand-900 dark:via-brand-700 dark:to-indigo-800 rounded-2xl overflow-hidden mb-6 print:hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 44px 44px;"></div>
        <div class="relative flex items-center justify-between px-5 pt-5">
            <a href="{{ route('scis.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
            <div class="flex items-center gap-2">
                <button @click="$dispatch('open-modal', 'edit-sci-{{ $sci->id }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Modifier
                </button>
                <form method="POST" action="{{ route('scis.destroy', $sci) }}" class="inline" onsubmit="return confirm('Supprimer cette SCI ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center p-2 bg-white/10 hover:bg-red-500/80 backdrop-blur-sm rounded-lg text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        <div class="relative text-center px-6 pb-8 pt-6">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-sm text-white border border-white/20">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $sci->is_active ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                {{ $sci->is_active ? 'Actif' : 'Inactif' }}
            </span>
            @if($sci->logo_path)
                <div class="mt-4"><img class="h-14 w-14 rounded-xl object-cover ring-2 ring-white/20 mx-auto" src="{{ Storage::url($sci->logo_path) }}" alt="{{ $sci->name }}"></div>
            @endif
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-3">{{ $sci->name }}</h1>
            @if($sci->address)
                <p class="text-white/70 mt-2">{{ $sci->address }}</p>
            @endif
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-stat-card title="Biens immobiliers" :value="(string) ($sci->properties_count ?? $properties->count())" icon="building" color="blue" />
        <x-stat-card title="Locataires" :value="(string) ($sci->tenants_count ?? 0)" icon="users" color="green" />
        <x-stat-card title="Baux actifs" :value="(string) ($sci->active_leases_count ?? $leases->count())" icon="document" color="brand" />
    </div>

    {{-- Detail Card --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle mb-6 overflow-hidden">
        <div class="px-6 py-5 border-b border-theme-subtle">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Informations legales</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">RCCM</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $sci->rccm ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">IFU</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $sci->ifu ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 py-5 border-b border-theme-subtle">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Contact</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Email</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $sci->email ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Telephone</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $sci->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @if($sci->bank_name || $sci->bank_iban)
        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Informations bancaires</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Banque</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $sci->bank_name ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">IBAN</p>
                        <p class="text-sm font-semibold text-on-surface font-mono text-xs">{{ $sci->bank_iban ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Properties Table --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-theme-subtle flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-on-surface">Biens de cette SCI</h3>
        </div>

        @if($properties->count())
            <table class="min-w-full divide-y divide-theme-subtle">
                <thead class="">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Référence</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Adresse</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Statut</th>
                        <th class="px-6 py-3 text-right text-sm font-medium text-on-surface-faint">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-theme-subtle">
                    @foreach($properties as $property)
                        <tr class="hover:bg-surface-hover/50">
                            <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-on-surface">{{ $property->reference }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-muted">{{ ucfirst($property->type) }}</td>
                            <td class="px-6 py-5 text-sm text-on-surface-muted">{{ $property->address }}, {{ $property->city }}</td>
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
                                <a href="{{ route('properties.show', $property) }}" class="text-brand-600 dark:text-brand-400 hover:text-brand-900 dark:hover:text-brand-300">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-8 text-center text-sm text-on-surface-muted">Aucun bien pour cette SCI.</div>
        @endif
    </div>

    {{-- Active Leases --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden">
        <div class="px-6 py-4 border-b border-theme-subtle flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center">
                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-on-surface">Baux actifs</h3>
        </div>

        @if($leases->count())
            <table class="min-w-full divide-y divide-theme-subtle">
                <thead class="">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Bien</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Locataire</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Loyer</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Début</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Fin</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-on-surface-faint">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-theme-subtle">
                    @foreach($leases as $lease)
                        <tr class="hover:bg-surface-hover/50">
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface">{{ $lease->property->reference ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-muted">{{ $lease->tenant->full_name ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-muted">{{ number_format($lease->rent_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-muted">{{ $lease->start_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-muted">{{ $lease->end_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <x-badge type="success">{{ ucfirst($lease->status) }}</x-badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-8 text-center text-sm text-on-surface-muted">Aucun bail actif pour cette SCI.</div>
        @endif
    </div>

    {{-- Edit SCI Modal --}}
    <x-form-modal name="edit-sci-{{ $sci->id }}" title="Modifier {{ $sci->name }}" :action="route('scis.update', $sci)" method="PUT" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M3.75 3v18m16.5-18v18M5.25 6h.008v.008H5.25V6zm0 3h.008v.008H5.25V9zm0 3h.008v.008H5.25V12zm6-6h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12zm6-6h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12z"/></svg>' iconColor="text-amber-500">
        @include('scis._form', ['sci' => $sci])
    </x-form-modal>
@endsection
