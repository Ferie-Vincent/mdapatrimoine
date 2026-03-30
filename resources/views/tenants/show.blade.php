@extends('layouts.app')

@section('title', $tenant->full_name)

@section('content')
    {{-- Hero Header --}}
    <div class="relative bg-gradient-to-br from-brand-700 via-brand-500 to-indigo-500 dark:from-brand-900 dark:via-brand-700 dark:to-indigo-800 rounded-2xl overflow-hidden mb-6 print:hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 44px 44px;"></div>
        <div class="relative flex items-center justify-between px-5 pt-5">
            <a href="{{ route('tenants.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center p-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
                @can('update', $tenant)
                    <button @click="$dispatch('open-modal', 'edit-tenant-{{ $tenant->id }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Modifier
                    </button>
                @endcan
                @can('delete', $tenant)
                    <form method="POST" action="{{ route('tenants.destroy', $tenant) }}" class="inline" onsubmit="return confirm('Supprimer ce locataire ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center p-2 bg-white/10 hover:bg-red-500/80 backdrop-blur-sm rounded-lg text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="relative text-center px-6 pb-8 pt-6">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-sm text-white border border-white/20">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $tenant->is_active ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                {{ $tenant->is_active ? 'Actif' : 'Inactif' }}
            </span>
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-3">{{ $tenant->full_name }}</h1>
            <p class="text-white/70 mt-2">{{ $tenant->sci->name }}</p>
            <div class="mt-3 flex items-center justify-center flex-wrap gap-4 text-sm text-white/60">
                @if($tenant->phone)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $tenant->phone }}
                    </span>
                @endif
                @if($tenant->email)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $tenant->email }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Identity Card --}}
            <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden">

                {{-- Contact info --}}
                <div class="px-6 py-5 border-b border-theme-subtle">
                    <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Coordonnées</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-brand-100 dark:bg-brand-900/40 dark:bg-brand-900/40 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-faint">Téléphone</p>
                                <p class="text-sm font-semibold text-on-surface">{{ $tenant->phone }}</p>
                            </div>
                        </div>
                        @if($tenant->phone_secondary)
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-surface-hover flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-on-surface-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-faint">Tél. secondaire</p>
                                <p class="text-sm font-semibold text-on-surface">{{ $tenant->phone_secondary }}</p>
                            </div>
                        </div>
                        @endif
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-violet-100 dark:bg-violet-900/40 dark:bg-violet-900/40 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-faint">Email</p>
                                <p class="text-sm font-semibold text-on-surface">{{ $tenant->email ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/40 dark:bg-amber-900/40 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-faint">Adresse</p>
                                <p class="text-sm font-semibold text-on-surface">{{ $tenant->address ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ID Document --}}
                <div class="px-6 py-5 border-b border-theme-subtle">
                    <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Pièce d'identité</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-surface-hover rounded-xl p-3 text-center">
                            <p class="text-xs text-on-surface-faint">Type</p>
                            <p class="text-sm font-bold text-on-surface mt-0.5">{{ $tenant->id_type ?? '-' }}</p>
                        </div>
                        <div class="bg-surface-hover rounded-xl p-3 text-center">
                            <p class="text-xs text-on-surface-faint">Numéro</p>
                            <p class="text-sm font-bold text-on-surface mt-0.5">{{ $tenant->id_number ?? '-' }}</p>
                        </div>
                        <div class="bg-surface-hover rounded-xl p-3 text-center">
                            <p class="text-xs text-on-surface-faint">Expiration</p>
                            <p class="text-sm font-bold text-on-surface mt-0.5">{{ $tenant->id_expiration ? $tenant->id_expiration->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div class="bg-surface-hover rounded-xl p-3 text-center">
                            <p class="text-xs text-on-surface-faint">Document</p>
                            @if($tenant->id_file_path)
                                <a href="{{ Storage::url($tenant->id_file_path) }}" target="_blank" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 mt-0.5 inline-block">Voir</a>
                            @else
                                <p class="text-sm font-bold text-on-surface mt-0.5">-</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Professional --}}
                <div class="px-6 py-5 border-b border-theme-subtle">
                    <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Informations professionnelles</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-faint">Profession</p>
                                <p class="text-sm font-semibold text-on-surface">{{ $tenant->profession ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-faint">Employeur</p>
                                <p class="text-sm font-semibold text-on-surface">{{ $tenant->employer ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Emergency & Guarantor --}}
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-3">Contact d'urgence</p>
                            <div class="bg-red-50/50 dark:bg-red-950/30 dark:bg-red-950/30 rounded-xl p-4 border border-red-100 dark:border-red-800/30 dark:border-red-900/40 space-y-2">
                                <div>
                                    <p class="text-xs text-red-400">Nom</p>
                                    <p class="text-sm font-semibold text-on-surface">{{ $tenant->emergency_contact_name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-red-400">Téléphone</p>
                                    <p class="text-sm font-semibold text-on-surface">{{ $tenant->emergency_contact_phone ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-3">Garant / Caution</p>
                            <div class="bg-amber-50/50 dark:bg-amber-950/30 dark:bg-amber-950/30 rounded-xl p-4 border border-amber-100 dark:border-amber-800/30 dark:border-amber-900/40 space-y-2">
                                <div>
                                    <p class="text-xs text-amber-500">Nom</p>
                                    <p class="text-sm font-semibold text-on-surface">{{ $tenant->guarantor_name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-amber-500">Téléphone</p>
                                    <p class="text-sm font-semibold text-on-surface">{{ $tenant->guarantor_phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-amber-500">Adresse</p>
                                    <p class="text-sm font-semibold text-on-surface">{{ $tenant->guarantor_address ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">
            {{-- Cumulative Summary --}}
            @php
                $allRent = $leaseHistory->sum('rent_amount');
                $allCharges = $leaseHistory->sum('charges_amount');
                $allDeposit = $leaseHistory->sum('deposit_amount');
                $allDue = $leaseHistory->flatMap->leaseMonthlies->sum('total_due');
                $allPaid = $leaseHistory->flatMap->leaseMonthlies->sum('paid_amount');
                $allBalance = $allDue - $allPaid;
            @endphp
            <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden">
                <div class="px-5 py-4 border-b border-theme-subtle flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 dark:bg-green-900/40 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-on-surface">Cumul tous baux</h3>
                    <span class="ml-auto text-xs text-on-surface-faint">{{ $leaseHistory->count() }} bail(s)</span>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-on-surface-faint">Total loyers dus</span>
                        <span class="text-sm font-semibold text-on-surface">{{ number_format($allDue, 0, ',', ' ') }} F</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-on-surface-faint">Total payé</span>
                        <span class="text-sm font-semibold text-emerald-600">{{ number_format($allPaid, 0, ',', ' ') }} F</span>
                    </div>
                    <div class="border-t border-theme-subtle pt-3 flex justify-between items-center">
                        <span class="text-sm font-semibold text-on-surface-secondary">Solde global</span>
                        <span class="text-lg font-bold {{ $allBalance > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ number_format($allBalance, 0, ',', ' ') }} F
                        </span>
                    </div>
                    <div class="border-t border-theme-subtle pt-3 grid grid-cols-2 gap-3">
                        <div class="bg-surface-hover rounded-xl p-3">
                            <p class="text-xs text-on-surface-faint">Total charges</p>
                            <p class="text-sm font-bold text-on-surface">{{ number_format($allCharges, 0, ',', ' ') }} F</p>
                        </div>
                        <div class="bg-surface-hover rounded-xl p-3">
                            <p class="text-xs text-on-surface-faint">Total cautions</p>
                            <p class="text-sm font-bold text-on-surface">{{ number_format($allDeposit, 0, ',', ' ') }} F</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- All Leases --}}
            @forelse($leaseHistory as $lease)
                @php
                    $isActive = $lease->status === 'actif';
                    $monthlies = $lease->leaseMonthlies ?? collect();
                    $leaseDue = $monthlies->sum('total_due');
                    $leasePaid = $monthlies->sum('paid_amount');
                    $leaseBalance = $leaseDue - $leasePaid;
                @endphp
                <div class="bg-surface rounded-2xl border {{ $isActive ? 'border-emerald-200 dark:border-emerald-800/30 dark:border-emerald-800/40 ring-1 ring-emerald-100 dark:ring-emerald-900/30' : 'border-theme-subtle' }} overflow-hidden">
                    <div class="px-5 py-4 border-b {{ $isActive ? 'border-emerald-100 dark:border-emerald-800/30 dark:border-emerald-800/30 bg-emerald-50/30 dark:bg-emerald-950/30 dark:bg-emerald-950/20' : 'border-theme-subtle' }} flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg {{ $isActive ? 'bg-emerald-100 dark:bg-emerald-900/40 dark:bg-emerald-900/40' : 'bg-surface-alt' }} flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $isActive ? 'text-emerald-600' : 'text-on-surface-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('leases.show', $lease) }}" class="text-sm font-semibold text-on-surface hover:text-brand-600 dark:text-brand-400 transition truncate">
                                    {{ $lease->property->reference ?? '-' }}
                                </a>
                                @if($isActive)
                                    <x-badge type="success">Actif</x-badge>
                                @elseif($lease->status === 'termine')
                                    <x-badge type="default">Terminé</x-badge>
                                @elseif($lease->status === 'resilie')
                                    <x-badge type="danger">Résilié</x-badge>
                                @else
                                    <x-badge type="warning">{{ ucfirst($lease->status) }}</x-badge>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="bg-brand-50/60 dark:bg-brand-950/30 dark:bg-brand-950/30 rounded-xl p-4 border border-brand-100 dark:border-gray-600 dark:border-gray-600">
                            <p class="text-xs text-brand-500">Loyer mensuel</p>
                            <p class="text-xl font-bold text-on-surface">{{ number_format($lease->rent_amount, 0, ',', ' ') }} <span class="text-sm font-medium text-on-surface-muted">FCFA</span></p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-surface-hover rounded-xl p-3">
                                <p class="text-xs text-on-surface-faint">Charges</p>
                                <p class="text-sm font-bold text-on-surface">{{ number_format($lease->charges_amount, 0, ',', ' ') }} F</p>
                            </div>
                            <div class="bg-surface-hover rounded-xl p-3">
                                <p class="text-xs text-on-surface-faint">Caution</p>
                                <p class="text-sm font-bold text-on-surface">{{ number_format($lease->deposit_amount, 0, ',', ' ') }} F</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="pl-3 border-l-2 border-brand-200 dark:border-gray-600">
                                <p class="text-xs text-on-surface-faint">Début</p>
                                <p class="text-sm font-semibold text-on-surface">{{ $lease->start_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="pl-3 border-l-2 border-orange-200 dark:border-orange-800/30">
                                <p class="text-xs text-on-surface-faint">Fin</p>
                                <p class="text-sm font-semibold text-on-surface">{{ $lease->end_date ? $lease->end_date->format('d/m/Y') : '-' }}</p>
                            </div>
                        </div>
                        {{-- Per-lease payment summary --}}
                        @if($monthlies->count())
                            <div class="border-t border-theme-subtle pt-3 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-on-surface-faint">Dû</span>
                                    <span class="text-sm font-semibold text-on-surface">{{ number_format($leaseDue, 0, ',', ' ') }} F</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-on-surface-faint">Payé</span>
                                    <span class="text-sm font-semibold text-emerald-600">{{ number_format($leasePaid, 0, ',', ' ') }} F</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-semibold text-on-surface-secondary">Solde</span>
                                    <span class="text-sm font-bold {{ $leaseBalance > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ number_format($leaseBalance, 0, ',', ' ') }} F
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden">
                    <div class="px-5 py-4 border-b border-theme-subtle flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-surface-hover flex items-center justify-center">
                            <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-on-surface">Baux</h3>
                    </div>
                    <x-empty-state message="Aucun bail pour ce locataire." />
                </div>
            @endforelse
        </div>
    </div>

    {{-- Lease History Table --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-theme-subtle flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-surface-hover flex items-center justify-center">
                <svg class="w-4 h-4 text-on-surface-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-on-surface">Historique des baux</h3>
        </div>
        @if($leaseHistory->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme-subtle">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Bien</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Loyer</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Dû</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Payé</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Solde</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Période</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme-subtle">
                        @foreach($leaseHistory as $lease)
                            @php
                                $m = $lease->leaseMonthlies ?? collect();
                                $due = $m->sum('total_due');
                                $paid = $m->sum('paid_amount');
                                $bal = $due - $paid;
                            @endphp
                            <tr class="hover:bg-surface-hover/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap text-sm">
                                    <a href="{{ route('leases.show', $lease) }}" class="text-brand-600 dark:text-brand-400 hover:text-brand-900 dark:hover:text-brand-300 font-medium">{{ $lease->property->reference ?? '-' }}</a>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-on-surface text-right">{{ number_format($lease->rent_amount, 0, ',', ' ') }} F</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary text-right">{{ number_format($due, 0, ',', ' ') }} F</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-emerald-600 font-medium text-right">{{ number_format($paid, 0, ',', ' ') }} F</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-right {{ $bal > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($bal, 0, ',', ' ') }} F</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">
                                    {{ $lease->start_date->format('d/m/Y') }} — {{ $lease->end_date ? $lease->end_date->format('d/m/Y') : '...' }}
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($lease->status === 'actif')
                                        <x-badge type="success">Actif</x-badge>
                                    @elseif($lease->status === 'termine')
                                        <x-badge type="default">Terminé</x-badge>
                                    @elseif($lease->status === 'resilie')
                                        <x-badge type="danger">Résilié</x-badge>
                                    @else
                                        <x-badge type="warning">{{ ucfirst($lease->status) }}</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-empty-state message="Aucun bail pour ce locataire." />
        @endif
    </div>

    {{-- Edit Tenant Wizard Modal --}}
    @can('update', $tenant)
        <x-wizard-modal name="edit-tenant-{{ $tenant->id }}" title="Modifier {{ $tenant->full_name }}" :action="route('tenants.update', $tenant)" method="PUT" :hasFiles="true"
            :steps="['Identite', 'Piece d\'identite', 'Professionnel', 'Contact urgence', 'Garant']" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>' iconColor="text-blue-500">
            @include('tenants._wizard_steps', ['t' => $tenant])
        </x-wizard-modal>
    @endcan
@endsection
