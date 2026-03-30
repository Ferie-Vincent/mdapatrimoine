@extends('layouts.app')

@section('title', 'Bail #' . $lease->id)

@section('content')
    {{-- Hero Header --}}
    @php
        $statusColors = [
            'actif' => 'bg-emerald-400/20 text-emerald-300 ring-emerald-400/30',
            'termine' => 'bg-gray-400/20 text-gray-300 ring-gray-400/30',
            'resilie' => 'bg-red-400/20 text-red-300 ring-red-400/30',
            'en_attente' => 'bg-amber-400/20 text-amber-300 ring-amber-400/30',
        ];
        $statusLabels = ['actif' => 'Actif', 'termine' => 'Terminé', 'resilie' => 'Résilié', 'en_attente' => 'En attente'];
    @endphp
    <div class="relative bg-gradient-to-br from-brand-700 via-brand-500 to-indigo-500 dark:from-brand-900 dark:via-brand-700 dark:to-indigo-800 rounded-2xl overflow-hidden mb-6 print:hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 44px 44px;"></div>
        <div class="relative flex items-center justify-between px-5 pt-5">
            <a href="{{ route('leases.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center p-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
                @if(!in_array($lease->status, ['resilie', 'termine']))
                    @can('update', $lease)
                        <button @click="$dispatch('open-modal', 'edit-lease-{{ $lease->id }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Modifier
                        </button>
                    @endcan
                    @can('update', $lease)
                        @if($lease->status === 'en_attente')
                            <form method="POST" action="{{ route('leases.activate', $lease) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-500/20 hover:bg-emerald-500/30 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Activer
                                </button>
                            </form>
                        @endif
                    @endcan
                    @can('update', $lease)
                        @if($lease->status === 'actif')
                            <button type="button" x-data x-on:click="$dispatch('open-modal', 'terminate-lease')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-red-500/20 hover:bg-red-500/30 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                Résilier
                            </button>
                        @endif
                    @endcan
                @endif
            </div>
        </div>
        <div class="relative text-center px-6 pb-8 pt-6">
            <div class="flex items-center justify-center gap-2">
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold ring-1 {{ $statusColors[$lease->status] ?? 'bg-gray-400/20 text-gray-300 ring-gray-400/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $lease->status === 'actif' ? 'bg-emerald-400' : ($lease->status === 'resilie' ? 'bg-red-400' : ($lease->status === 'en_attente' ? 'bg-amber-400' : 'bg-gray-400')) }}"></span>
                    {{ $statusLabels[$lease->status] ?? ucfirst($lease->status) }}
                </span>
                @if($lease->isMeuble())
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-amber-400/20 text-amber-300 ring-1 ring-amber-400/30">Meublé</span>
                @endif
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-3">{{ $lease->isMeuble() ? 'Séjour' : 'Bail' }} #{{ $lease->id }}</h1>
            <p class="text-white/70 mt-2">{{ $lease->sci->name ?? '' }}</p>
            <div class="mt-3 flex items-center justify-center flex-wrap gap-4 text-sm text-white/60">
                @if($lease->property)
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ $lease->property->reference }}
                    </span>
                @endif
                @if($lease->tenant)
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $lease->tenant->full_name }}
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $lease->start_date?->format('d/m/Y') }} — {{ $lease->end_date?->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    @error('termination')
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/30 rounded-2xl text-sm text-red-700 dark:text-red-300">
            {{ $message }}
        </div>
    @enderror

    @php
        $depositDueAmount = (float)($lease->deposit_amount ?? 0) - (float)($lease->deposit_returned_amount ?? 0);
        $isTerminated = in_array($lease->status, ['resilie', 'termine']);
        $depositFullyRefunded = $depositDueAmount <= 0;
        $isArchived = $isTerminated && $depositFullyRefunded;
    @endphp
    @if($isArchived)
        <div class="mb-6 p-4 bg-surface-alt border border-theme rounded-2xl text-sm text-on-surface-secondary flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-surface-hover flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <span>Ce bail est <strong>archivé</strong> ({{ $lease->status === 'resilie' ? 'résilié' : 'terminé' }}{{ $lease->termination_date ? ' le ' . $lease->termination_date->format('d/m/Y') : '' }}). Aucune action n'est possible.</span>
        </div>
    @elseif($isTerminated && !$depositFullyRefunded)
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/30 rounded-2xl text-sm text-amber-800 dark:text-amber-200 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <span>Ce bail est <strong>{{ $lease->status === 'resilie' ? 'résilié' : 'terminé' }}</strong>{{ $lease->termination_date ? ' le ' . $lease->termination_date->format('d/m/Y') : '' }}. La caution de <strong>{{ number_format($depositDueAmount, 0, ',', ' ') }} FCFA</strong> reste à rembourser avant archivage.</span>
            </div>
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'refund-deposit')"
                    class="inline-flex items-center shrink-0 px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition shadow-sm">
                Rembourser
            </button>
        </div>
    @endif

    {{-- Lease detail card --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle mb-6 overflow-hidden">
        {{-- Parties: Bien & Locataire --}}
        <div class="px-6 py-5 border-b border-theme-subtle bg-surface-hover/50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4.5 h-4.5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-on-surface-faint uppercase tracking-wider">Bien immobilier</p>
                        @if($lease->property)
                            <a href="{{ route('properties.show', $lease->property) }}" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 transition">
                                {{ $lease->property->reference }}
                            </a>
                            <p class="text-xs text-on-surface-muted mt-0.5">{{ $lease->property->address }}, {{ $lease->property->city }}</p>
                        @else
                            <p class="text-sm text-on-surface-faint">-</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4.5 h-4.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-on-surface-faint uppercase tracking-wider">Locataire</p>
                        @if($lease->tenant)
                            <a href="{{ route('tenants.show', $lease->tenant) }}" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 transition">
                                {{ $lease->tenant->full_name }}
                            </a>
                            @if($lease->tenant->phone)
                                <p class="text-xs text-on-surface-muted mt-0.5">{{ $lease->tenant->phone }}</p>
                            @endif
                        @else
                            <p class="text-sm text-on-surface-faint">-</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Dates --}}
        <div class="px-6 py-5 border-b border-theme-subtle">
            @if($lease->isMeuble())
                <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Période du séjour</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="relative pl-4 border-l-2 border-brand-200 dark:border-gray-600">
                        <p class="text-xs text-on-surface-muted">Check-in</p>
                        <p class="text-sm font-semibold text-on-surface mt-0.5">{{ $lease->check_in_date?->format('d/m/Y') }}</p>
                    </div>
                    <div class="relative pl-4 border-l-2 border-orange-200 dark:border-orange-800/30">
                        <p class="text-xs text-on-surface-muted">Check-out</p>
                        <p class="text-sm font-semibold text-on-surface mt-0.5">{{ $lease->check_out_date?->format('d/m/Y') }}</p>
                    </div>
                    <div class="relative pl-4 border-l-2 border-theme">
                        <p class="text-xs text-on-surface-muted">Nuits</p>
                        <p class="text-sm font-semibold text-on-surface mt-0.5">{{ $lease->nights_count }} nuits</p>
                    </div>
                    <div class="relative pl-4 border-l-2 border-purple-200 dark:border-purple-800/30">
                        <p class="text-xs text-on-surface-muted">Tarif / 24h</p>
                        <p class="text-sm font-semibold text-on-surface mt-0.5">{{ number_format($lease->daily_rate, 0, ',', ' ') }} F</p>
                    </div>
                </div>
            @else
                <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Période du bail</p>
                <div class="grid grid-cols-3 gap-4">
                    <div class="relative pl-4 border-l-2 border-brand-200 dark:border-gray-600">
                        <p class="text-xs text-on-surface-muted">Date de début</p>
                        <p class="text-sm font-semibold text-on-surface mt-0.5">{{ $lease->start_date?->format('d/m/Y') }}</p>
                    </div>
                    <div class="relative pl-4 border-l-2 border-orange-200 dark:border-orange-800/30">
                        <p class="text-xs text-on-surface-muted">Date de fin</p>
                        <p class="text-sm font-semibold text-on-surface mt-0.5">{{ $lease->end_date?->format('d/m/Y') }}</p>
                    </div>
                    <div class="relative pl-4 border-l-2 border-theme">
                        <p class="text-xs text-on-surface-muted">Durée</p>
                        <p class="text-sm font-semibold text-on-surface mt-0.5">{{ $lease->duration_months }} mois</p>
                    </div>
                </div>
            @endif
            @php
                $now = now();
                $start = $lease->start_date;
                $end = $lease->end_date;
                $progress = ($start && $end && $end->gt($start)) ? min(100, max(0, round($start->diffInDays($now) / $start->diffInDays($end) * 100))) : 0;
            @endphp
            @if($lease->status === 'actif' && $start && $end)
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs text-on-surface-faint mb-1.5">
                        <span>Progression</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-surface-alt rounded-full h-2">
                        <div class="h-2 rounded-full transition-all {{ $progress > 80 ? 'bg-orange-500' : 'bg-brand-500' }}" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Financier --}}
        <div class="px-6 py-5 border-b border-theme-subtle">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Informations financières</p>
            @if($lease->isMeuble())
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-surface-hover rounded-xl p-4 border border-theme-subtle">
                        <p class="text-xs font-medium text-on-surface-secondary mb-1">Sous-total</p>
                        <p class="text-lg font-bold text-on-surface">{{ number_format((float)$lease->daily_rate * $lease->nights_count, 0, ',', ' ') }} <span class="text-sm font-medium text-on-surface-muted">F</span></p>
                        <p class="text-xs text-on-surface-faint mt-0.5">{{ number_format($lease->daily_rate, 0, ',', ' ') }} F x {{ $lease->nights_count }} nuits</p>
                    </div>
                    @if((float)$lease->discount_rate > 0)
                        <div class="bg-green-50/60 dark:bg-green-950/30 rounded-xl p-4 border border-green-100 dark:border-green-800/30">
                            <p class="text-xs font-medium text-green-700 dark:text-green-300 mb-1">Remise ({{ $lease->discount_rate }}%)</p>
                            <p class="text-lg font-bold text-green-700 dark:text-green-300">- {{ number_format($lease->discount_amount, 0, ',', ' ') }} <span class="text-sm font-medium text-green-500">F</span></p>
                        </div>
                    @endif
                    <div class="bg-brand-50/60 dark:bg-brand-950/30 rounded-xl p-4 border border-brand-100 dark:border-gray-600">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center">
                                <svg class="w-3 h-3 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-xs font-medium text-brand-700 dark:text-brand-300">Total a payer</p>
                        </div>
                        <p class="text-xl font-bold text-on-surface">{{ number_format($lease->total_amount, 0, ',', ' ') }} <span class="text-sm font-medium text-on-surface-muted">FCFA</span></p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Loyer --}}
                    <div class="bg-brand-50/60 dark:bg-brand-950/30 rounded-xl p-4 border border-brand-100 dark:border-gray-600">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-xs font-medium text-brand-700 dark:text-brand-300">Loyer mensuel</p>
                        </div>
                        <p class="text-xl font-bold text-on-surface">{{ number_format($lease->rent_amount, 0, ',', ' ') }} <span class="text-sm font-medium text-on-surface-muted">FCFA</span></p>
                    </div>

                    {{-- Frais d'agence --}}
                    <div class="bg-surface-hover rounded-xl p-4 border border-theme-subtle">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-surface-hover flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-on-surface-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <p class="text-xs font-medium text-on-surface-secondary">Frais d'agence</p>
                        </div>
                        <p class="text-xl font-bold text-on-surface">{{ number_format($lease->charges_amount, 0, ',', ' ') }} <span class="text-sm font-medium text-on-surface-muted">FCFA</span></p>
                    </div>

                    {{-- Caution --}}
                    @php
                        $cautionRemaining = (float)($lease->deposit_amount ?? 0) - (float)($lease->deposit_returned_amount ?? 0);
                        $cautionReturned = (float)($lease->deposit_returned_amount ?? 0);
                        $cautionFull = $cautionRemaining <= 0 && $cautionReturned > 0;
                    @endphp
                    <div class="rounded-xl p-4 border {{ $cautionFull ? 'bg-emerald-50/60 dark:bg-emerald-950/30 border-emerald-100 dark:border-emerald-800/30' : 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-100 dark:border-amber-800/30' }}">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-lg {{ $cautionFull ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-amber-100 dark:bg-amber-900/40' }} flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 {{ $cautionFull ? 'text-emerald-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <p class="text-xs font-medium {{ $cautionFull ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }}">Caution</p>
                        </div>
                        <p class="text-xl font-bold text-on-surface">{{ number_format($lease->deposit_amount, 0, ',', ' ') }} <span class="text-sm font-medium text-on-surface-muted">FCFA</span></p>
                        @if($cautionReturned > 0)
                            <p class="mt-1.5 text-xs font-medium {{ $cautionFull ? 'text-emerald-600' : 'text-orange-600' }}">
                                @if($cautionFull)
                                    <svg class="w-3 h-3 inline mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Intégralement remboursé
                                @else
                                    Remboursé : {{ number_format($cautionReturned, 0, ',', ' ') }} FCFA — Reste : {{ number_format($cautionRemaining, 0, ',', ' ') }} FCFA
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Conditions --}}
        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Conditions</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Mode de paiement</p>
                        @php
                            $methodLabels = ['virement' => 'Virement', 'especes' => 'Espèces', 'cheque' => 'Chèque', 'mobile_money' => 'Mobile Money', 'depot_bancaire' => 'Dépôt bancaire', 'versement_especes' => 'Versement espèces sur compte', 'autre' => 'Autre'];
                        @endphp
                        <p class="text-sm font-semibold text-on-surface">{{ $methodLabels[$lease->payment_method] ?? ucfirst($lease->payment_method ?? '-') }}</p>
                    </div>
                </div>
                @if(!$lease->isMeuble())
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-faint">Jour d'échéance</p>
                            <p class="text-sm font-semibold text-on-surface">Le {{ $lease->due_day }} du mois</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-faint">Pénalité de retard</p>
                            <p class="text-sm font-semibold text-on-surface">{{ $lease->penalty_rate }}% <span class="font-normal text-on-surface-muted">après {{ $lease->penalty_delay_days }} jours</span></p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Notes --}}
        @if($lease->notes)
            <div class="px-6 py-4 border-t border-theme-subtle bg-surface-hover/30">
                <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-2">Notes</p>
                <p class="text-sm text-on-surface-secondary whitespace-pre-line">{{ $lease->notes }}</p>
            </div>
        @endif

        {{-- Termination --}}
        @if($lease->termination_date)
            <div class="px-6 py-5 border-t border-red-200 dark:border-red-800/30 bg-red-50 dark:bg-red-950/30">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-red-800 dark:text-red-200">Bail résilié</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-10">
                    <div>
                        <p class="text-xs text-red-500">Date de sortie</p>
                        <p class="text-sm font-semibold text-red-900 dark:text-red-300">{{ $lease->termination_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-red-500">Motif</p>
                        <p class="text-sm font-semibold text-red-900 dark:text-red-300">{{ $lease->termination_reason ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Entry receipts --}}
    @php
        $entryReceipts = ($documents ?? collect())->filter(fn ($d) => in_array($d->type, ['recu_entree_caution', 'recu_entree_agence']));
    @endphp
    @if($entryReceipts->count())
        <div class="bg-surface rounded-2xl border border-theme-subtle mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-theme-subtle flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-lg font-medium text-on-surface">Recus d'entree</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($entryReceipts as $doc)
                        <a href="{{ route('documents.download', $doc) }}" target="_blank"
                           class="flex items-center gap-3 p-3 rounded-xl border border-theme-subtle hover:bg-surface-hover transition group">
                            <div class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-950/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-on-surface group-hover:text-brand-600 dark:text-brand-400 transition">
                                    @if($doc->type === 'recu_entree_caution')
                                        Recu Caution & Avances sur loyers
                                    @else
                                        Recu Frais d'agence
                                    @endif
                                </p>
                                <p class="text-xs text-on-surface-muted">{{ $doc->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <svg class="w-4 h-4 text-on-surface-faint group-hover:text-brand-500 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Monthlies section --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-theme-subtle">
            <h3 class="text-lg font-medium text-on-surface">{{ $lease->isMeuble() ? 'Paiement du sejour' : 'Échéances mensuelles' }}</h3>
        </div>
        @if($lease->leaseMonthlies && $lease->leaseMonthlies->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme-subtle">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Mois</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Total dû</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Payé</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Reste</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Statut</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme-subtle">
                        @foreach($lease->leaseMonthlies->sortByDesc('month') as $monthly)
                            <tr class="hover:bg-surface-hover/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-on-surface">{{ $monthly->month }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ number_format($monthly->total_due, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ number_format($monthly->paid_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ number_format($monthly->remaining_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <x-badge :type="$monthly->status" :label="ucfirst(str_replace('_', ' ', $monthly->status))" />
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    @if(!$isTerminated)
                                        @can('create', App\Models\Payment::class)
                                            @if($monthly->status !== 'paye')
                                                <button @click="$dispatch('open-modal', 'pay-monthly-{{ $monthly->id }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition shadow-sm">Payer</button>
                                            @endif
                                        @endcan
                                    @endif
                                    @can('create', App\Models\Document::class)
                                        @if($monthly->status === 'paye')
                                            <form method="POST" action="{{ route('documents.generate-quittance') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="lease_monthly_id" value="{{ $monthly->id }}">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/30 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800/40 dark:bg-blue-900/40 transition">Quittance</button>
                                            </form>
                                        @endif
                                    @endcan
                                    <a href="{{ route('monthlies.show', $monthly) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-on-surface-secondary bg-surface-hover border border-theme rounded-lg hover:bg-surface-alt transition">Voir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-empty-state message="Aucune échéance générée pour ce bail." />
        @endif
    </div>

    {{-- Documents section --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-theme-subtle">
            <h3 class="text-lg font-medium text-on-surface">Documents liés</h3>
        </div>
        @if($lease->documents && $lease->documents->count())
            <ul class="divide-y divide-theme-subtle">
                @foreach($lease->documents as $document)
                    <li class="px-6 py-4 flex items-center justify-between hover:bg-surface-hover/50 transition">
                        <div>
                            <p class="text-sm font-medium text-on-surface">{{ ucfirst(str_replace('_', ' ', $document->type)) }}</p>
                            <p class="text-sm text-on-surface-secondary">{{ $document->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('documents.download', $document) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-accent-green-600 bg-accent-green-50 dark:bg-accent-green-950/30 border border-accent-green-200 dark:border-accent-green-800/30 rounded-lg hover:bg-accent-green-100 dark:hover:bg-accent-green-900/40 transition">
                            Telecharger
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <x-empty-state message="Aucun document associé." />
        @endif
    </div>

    {{-- Deposit refund modal (available when deposit not fully refunded) --}}
    @if((float)($lease->deposit_amount ?? 0) > 0 && $depositDueAmount > 0)
    <x-modal name="refund-deposit" maxWidth="2xl">
        <div class="p-6">
            {{-- Header: title + close button --}}
            <div class="flex items-center justify-between mb-0">
                <h2 class="text-lg font-semibold text-on-surface">Remboursement de caution</h2>
                <button type="button" x-on:click="$dispatch('close-modal', 'refund-deposit')"
                        class="text-on-surface-faint hover:text-on-surface-secondary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <hr class="my-4 border-theme">

            {{-- Deposit summary --}}
            <div class="grid grid-cols-3 gap-4 mb-5">
                <div class="bg-surface-hover rounded-xl p-3">
                    <p class="text-xs text-on-surface-muted">Caution initiale</p>
                    <p class="text-base font-bold text-on-surface">{{ number_format((float)($lease->deposit_amount ?? 0), 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-green-50 dark:bg-green-950/30 rounded-xl p-3">
                    <p class="text-xs text-green-600">Deja rembourse</p>
                    <p class="text-base font-bold text-green-700 dark:text-green-300">{{ number_format((float)($lease->deposit_returned_amount ?? 0), 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-orange-50 dark:bg-orange-950/30 rounded-xl p-3">
                    <p class="text-xs text-orange-600">Reste a rembourser</p>
                    <p class="text-base font-bold text-orange-700 dark:text-orange-300">{{ number_format($depositDueAmount, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>

            {{-- Previous refunds history --}}
            @if($depositRefunds->count())
                <div class="mb-5">
                    <h4 class="text-sm font-medium text-on-surface-secondary mb-2">Historique des remboursements</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-theme-subtle text-sm">
                            <thead class="">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-on-surface-faint">Date</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-on-surface-faint">Montant</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-on-surface-faint">Mode</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-on-surface-faint">Reference</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-on-surface-faint">Justif.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-theme-subtle">
                                @foreach($depositRefunds as $refund)
                                    <tr>
                                        <td class="px-3 py-2 text-on-surface-secondary">{{ $refund->refunded_at->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2 text-right text-on-surface-secondary">{{ number_format((float)$refund->amount, 0, ',', ' ') }} FCFA</td>
                                        <td class="px-3 py-2 text-on-surface-secondary">
                                            @switch($refund->method)
                                                @case('especes') Especes @break
                                                @case('virement') Virement @break
                                                @case('cheque') Cheque @break
                                                @case('mobile_money') Mobile Money @break
                                                @case('depot_bancaire') Dépôt bancaire @break
                                                @case('versement_especes') Versement especes sur compte @break
                                                @default - @break
                                            @endswitch
                                        </td>
                                        <td class="px-3 py-2 text-on-surface-secondary">{{ $refund->reference ?? '-' }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($refund->receipt_path)
                                                <a href="{{ asset('storage/' . $refund->receipt_path) }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">
                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                                </a>
                                            @else
                                                <span class="text-on-surface-faint">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <hr class="my-4 border-theme">

            {{-- Refund form --}}
            <form method="POST" action="{{ route('payments.refund-deposit') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="lease_id" value="{{ $lease->id }}">

                @error('refund_amount')
                    <div class="mb-4 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/30 rounded-lg text-sm text-red-700 dark:text-red-300">{{ $message }}</div>
                @enderror

                <div class="grid grid-cols-2 gap-x-4 gap-y-5">
                    {{-- Montant --}}
                    <div>
                        <label for="refund_amount" class="block text-sm font-medium text-on-surface-secondary mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <x-money-input name="amount" id="refund_amount" :value="old('amount', $depositDueAmount)" :required="true" />
                        <p class="mt-1 text-xs text-on-surface-faint">Max : {{ number_format($depositDueAmount, 0, ',', ' ') }} FCFA</p>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label for="refund_date" class="block text-sm font-medium text-on-surface-secondary mb-1.5">Date de remboursement <span class="text-red-500">*</span></label>
                        <input type="date" name="refunded_at" id="refund_date"
                               value="{{ old('refunded_at', now()->format('Y-m-d')) }}" required
                               class="h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    </div>

                    {{-- Mode --}}
                    <div>
                        <label for="refund_method" class="block text-sm font-medium text-on-surface-secondary mb-1.5">Mode de remboursement <span class="text-red-500">*</span></label>
                        <select name="method" id="refund_method" required
                                class="h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            <option value="">Selectionner</option>
                            <option value="especes" {{ old('method') === 'especes' ? 'selected' : '' }}>Especes</option>
                            <option value="virement" {{ old('method') === 'virement' ? 'selected' : '' }}>Virement</option>
                            <option value="cheque" {{ old('method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="mobile_money" {{ old('method') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="depot_bancaire" {{ old('method') === 'depot_bancaire' ? 'selected' : '' }}>Dépôt bancaire</option>
                        </select>
                    </div>

                    {{-- Reference --}}
                    <div>
                        <label for="refund_reference" class="block text-sm font-medium text-on-surface-secondary mb-1.5">Reference</label>
                        <input type="text" name="reference" id="refund_reference" value="{{ old('reference') }}" placeholder="N° recu, N° virement..."
                               class="h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface placeholder:text-on-surface-faint focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    </div>

                    {{-- Note --}}
                    <div class="col-span-2">
                        <label for="refund_note" class="block text-sm font-medium text-on-surface-secondary mb-1.5">Note</label>
                        <textarea name="note" id="refund_note" rows="2" placeholder="Commentaire optionnel..."
                                  class="w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface placeholder:text-on-surface-faint focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ old('note') }}</textarea>
                    </div>

                    {{-- Justificatif --}}
                    <div class="col-span-2">
                        <x-file-upload name="receipt" id="refund_receipt" label="Justificatif (preuve)" />
                    </div>
                </div>

                {{-- Submit button --}}
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-5 py-3 text-sm font-medium text-white hover:bg-brand-700 transition shadow-sm">
                        Rembourser la caution
                    </button>
                    <button type="button" x-on:click="$dispatch('close-modal', 'refund-deposit')"
                            class="inline-flex items-center justify-center rounded-lg border border-theme px-5 py-3 text-sm font-medium text-on-surface-secondary bg-surface hover:bg-surface-hover transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
    @endif

    {{-- Edit Lease Wizard Modal --}}
    @if(!$isTerminated)
    @can('update', $lease)
        @if($lease->isMeuble())
            {{-- MEUBLE EDIT WIZARD --}}
            <x-wizard-modal name="edit-lease-{{ $lease->id }}" title="Modifier le sejour #{{ $lease->id }}" :action="route('leases.update', $lease)" method="PUT" :hasFiles="true"
                :steps="['Parties', 'Sejour', 'Conditions', 'Documents', 'Notes']"
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>' iconColor="text-purple-500">

                <div x-data="{
                    editCheckIn: '{{ $lease->check_in_date?->format('Y-m-d') }}',
                    editCheckOut: '{{ $lease->check_out_date?->format('Y-m-d') }}',
                    editDailyRate: {{ (float)($lease->daily_rate ?? 0) }},
                    editNights: 0, editSubtotal: 0, editDiscountRate: 0, editDiscountAmount: 0, editTotalAmount: 0
                }" x-effect="
                    if (editCheckIn && editCheckOut) {
                        const ci = new Date(editCheckIn + 'T00:00:00'), co = new Date(editCheckOut + 'T00:00:00');
                        editNights = Math.max(0, Math.round((co - ci) / 86400000));
                        editSubtotal = editDailyRate * editNights;
                        editDiscountRate = editNights >= 21 ? 15 : (editNights >= 14 ? 10 : (editNights >= 7 ? 5 : 0));
                        editDiscountAmount = Math.round(editSubtotal * editDiscountRate / 100);
                        editTotalAmount = editSubtotal - editDiscountAmount;
                    } else { editNights = 0; editSubtotal = 0; editDiscountRate = 0; editDiscountAmount = 0; editTotalAmount = 0; }
                ">

                {{-- Step 1: Parties --}}
                <div x-show="currentStep === 0" data-step="0">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">SCI</label>
                            <p class="mt-1 text-sm font-semibold text-on-surface">{{ $lease->sci->name ?? '-' }}</p>
                            <input type="hidden" name="sci_id" value="{{ $lease->sci_id }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Bien</label>
                            <p class="mt-1 text-sm font-semibold text-on-surface">{{ $lease->property->reference ?? '-' }} <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold rounded bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">Meuble</span></p>
                            <input type="hidden" name="property_id" value="{{ $lease->property_id }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Locataire</label>
                            <select name="tenant_id" required {{ $lease->status === 'actif' ? 'disabled' : '' }} class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden disabled:bg-surface-alt disabled:cursor-not-allowed">
                                <option value="">Selectionner</option>
                                @foreach($tenants ?? [] as $t)
                                    <option value="{{ $t->id }}" {{ $t->id == $lease->tenant_id ? 'selected' : '' }}>{{ $t->first_name }} {{ $t->last_name }}</option>
                                @endforeach
                            </select>
                            @if($lease->status === 'actif')
                                <input type="hidden" name="tenant_id" value="{{ $lease->tenant_id }}">
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Step 2: Sejour --}}
                <div x-show="currentStep === 1" data-step="1">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Date de check-in <span class="text-red-500">*</span></label>
                            <input type="date" name="check_in_date" x-model="editCheckIn" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Date de check-out <span class="text-red-500">*</span></label>
                            <input type="date" name="check_out_date" x-model="editCheckOut" :min="editCheckIn" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Nombre de nuits</label>
                            <input type="number" :value="editNights" readonly class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-surface-alt px-4 py-2.5 text-sm text-on-surface cursor-not-allowed">
                        </div>
                    </div>
                    <input type="hidden" name="daily_rate" :value="editDailyRate">
                    <div x-show="editNights > 0" x-transition class="mt-5 p-4 bg-gradient-to-br from-surface-hover to-surface border border-theme rounded-xl">
                        <p class="text-xs font-semibold text-on-surface-muted uppercase tracking-wider mb-3">Recapitulatif du sejour</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-2 py-1 text-xs rounded-full" :class="editDiscountRate === 0 ? 'bg-theme text-on-surface-secondary font-semibold' : 'bg-surface-alt text-on-surface-faint'">1-6 nuits : 0%</span>
                            <span class="px-2 py-1 text-xs rounded-full" :class="editDiscountRate === 5 ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-semibold' : 'bg-surface-alt text-on-surface-faint'">&ge; 7 nuits : -5%</span>
                            <span class="px-2 py-1 text-xs rounded-full" :class="editDiscountRate === 10 ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 font-semibold' : 'bg-surface-alt text-on-surface-faint'">&ge; 14 nuits : -10%</span>
                            <span class="px-2 py-1 text-xs rounded-full" :class="editDiscountRate === 15 ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 font-semibold' : 'bg-surface-alt text-on-surface-faint'">&ge; 21 nuits : -15%</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm"><span class="text-on-surface-secondary">Tarif / 24h</span><span class="font-medium" x-text="editDailyRate.toLocaleString('fr-FR') + ' F'"></span></div>
                            <div class="flex justify-between text-sm"><span class="text-on-surface-secondary">Sous-total (<span x-text="editNights"></span> nuits)</span><span class="font-medium" x-text="editSubtotal.toLocaleString('fr-FR') + ' F'"></span></div>
                            <div x-show="editDiscountRate > 0" class="flex justify-between text-sm text-green-600"><span>Remise (<span x-text="editDiscountRate"></span>%)</span><span class="font-medium">- <span x-text="editDiscountAmount.toLocaleString('fr-FR')"></span> F</span></div>
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-theme"><span class="text-on-surface">Total a payer</span><span class="text-brand-600 dark:text-brand-400" x-text="editTotalAmount.toLocaleString('fr-FR') + ' F'"></span></div>
                        </div>
                    </div>
                </div>

                {{-- Step 3: Conditions --}}
                <div x-show="currentStep === 2" data-step="2">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Mode de paiement</label>
                            <select name="payment_method" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <option value="">Selectionner</option>
                                <option value="especes" {{ $lease->payment_method === 'especes' ? 'selected' : '' }}>Especes</option>
                                <option value="virement" {{ $lease->payment_method === 'virement' ? 'selected' : '' }}>Virement</option>
                                <option value="cheque" {{ $lease->payment_method === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="mobile_money" {{ $lease->payment_method === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="depot_bancaire" {{ $lease->payment_method === 'depot_bancaire' ? 'selected' : '' }}>Dépôt bancaire</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Statut <span class="text-red-500">*</span></label>
                            <select name="status" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <option value="actif" {{ $lease->status === 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="en_attente" {{ $lease->status === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Step 4: Documents --}}
                <div x-show="currentStep === 3" data-step="3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-file-upload name="signed_lease" label="Bail signe (PDF)" accept=".pdf,.jpg,.jpeg,.png" hint="PDF, JPG ou PNG (max 10 Mo)" :current="$lease->signed_lease_path ? basename($lease->signed_lease_path) : null" />
                        <x-file-upload-multiple name="entry_inspection" label="Etat des lieux d'entree" accept=".jpg,.jpeg,.png,.pdf" hint="PDF, JPG ou PNG (max 10 Mo chacun)" :existing="$lease->entry_inspection_path ?? []" />
                    </div>
                </div>

                {{-- Step 5: Notes --}}
                <div x-show="currentStep === 4" data-step="4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface-secondary">Notes</label>
                        <textarea name="notes" rows="4" placeholder="Notes supplementaires..." class="mt-1.5 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ $lease->notes }}</textarea>
                    </div>
                </div>

                </div> {{-- End meuble wrapper --}}
            </x-wizard-modal>
        @else
            {{-- CLASSIQUE EDIT WIZARD --}}
            <x-wizard-modal name="edit-lease-{{ $lease->id }}" title="Modifier le bail #{{ $lease->id }}" :action="route('leases.update', $lease)" method="PUT" :hasFiles="true"
                :steps="['Parties', 'Termes du bail', 'Conditions', 'Documents', 'Dossier Excel', 'Notes']"
                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>' iconColor="text-purple-500">

                {{-- Step 1: Parties --}}
                <div x-show="currentStep === 0" data-step="0">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">SCI</label>
                            <p class="mt-1 text-sm font-semibold text-on-surface">{{ $lease->sci->name ?? '-' }}</p>
                            <input type="hidden" name="sci_id" value="{{ $lease->sci_id }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Bien <span class="text-red-500">*</span></label>
                            <select name="property_id" required {{ $lease->status === 'actif' ? 'disabled' : '' }} class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden disabled:bg-surface-alt disabled:cursor-not-allowed">
                                <option value="">Selectionner</option>
                                @foreach($properties ?? [] as $prop)
                                    <option value="{{ $prop->id }}" {{ $prop->id == $lease->property_id ? 'selected' : '' }}>{{ $prop->reference }} - {{ $prop->address }}</option>
                                @endforeach
                            </select>
                            @if($lease->status === 'actif')
                                <input type="hidden" name="property_id" value="{{ $lease->property_id }}">
                            @endif
                            <template x-if="errors.property_id"><p class="mt-1 text-sm text-red-600" x-text="errors.property_id[0]"></p></template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Locataire <span class="text-red-500">*</span></label>
                            <select name="tenant_id" required {{ $lease->status === 'actif' ? 'disabled' : '' }} class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden disabled:bg-surface-alt disabled:cursor-not-allowed">
                                <option value="">Selectionner</option>
                                @foreach($tenants ?? [] as $t)
                                    <option value="{{ $t->id }}" {{ $t->id == $lease->tenant_id ? 'selected' : '' }}>{{ $t->first_name }} {{ $t->last_name }}</option>
                                @endforeach
                            </select>
                            @if($lease->status === 'actif')
                                <input type="hidden" name="tenant_id" value="{{ $lease->tenant_id }}">
                            @endif
                            <template x-if="errors.tenant_id"><p class="mt-1 text-sm text-red-600" x-text="errors.tenant_id[0]"></p></template>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Termes du bail --}}
                <div x-show="currentStep === 1" data-step="1" x-data="leaseDates('{{ $lease->start_date?->format('Y-m-d') }}', '{{ $lease->end_date?->format('Y-m-d') }}')" x-effect="computeDuration()">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Date de debut <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" x-model="startDate" @change="onStartChange()" required class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Date de fin <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" x-model="endDate" :min="startDate" required class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Duree (mois)</label>
                            <input type="number" name="duration_months" :value="duration" min="1" readonly class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-surface-alt px-4 py-2.5 text-sm text-on-surface cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Loyer (FCFA) <span class="text-red-500">*</span></label>
                            <x-money-input name="rent_amount" :value="$lease->rent_amount" :required="true" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Frais d'agence (FCFA)</label>
                            <x-money-input name="charges_amount" :value="$lease->charges_amount" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Caution (FCFA)</label>
                            <x-money-input name="deposit_amount" :value="$lease->deposit_amount" />
                        </div>
                    </div>
                </div>

                {{-- Step 3: Conditions --}}
                <div x-show="currentStep === 2" data-step="2">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Mode de paiement</label>
                            <select name="payment_method" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <option value="">Selectionner</option>
                                <option value="especes" {{ $lease->payment_method === 'especes' ? 'selected' : '' }}>Especes</option>
                                <option value="virement" {{ $lease->payment_method === 'virement' ? 'selected' : '' }}>Virement</option>
                                <option value="cheque" {{ $lease->payment_method === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="mobile_money" {{ $lease->payment_method === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="depot_bancaire" {{ $lease->payment_method === 'depot_bancaire' ? 'selected' : '' }}>Dépôt bancaire</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Jour d'echeance</label>
                            <select name="due_day" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                @for($i = 1; $i <= 28; $i++)
                                    <option value="{{ $i }}" {{ ($lease->due_day ?? 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Statut <span class="text-red-500">*</span></label>
                            <select name="status" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <option value="actif" {{ $lease->status === 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="en_attente" {{ $lease->status === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Taux de penalite (%)</label>
                            <input type="number" name="penalty_rate" value="{{ $lease->penalty_rate }}" min="0" max="100" step="0.01" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Delai avant penalite (jours)</label>
                            <input type="number" name="penalty_delay_days" value="{{ $lease->penalty_delay_days }}" min="0" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                    </div>
                </div>

                {{-- Step 4: Documents --}}
                <div x-show="currentStep === 3" data-step="3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-file-upload name="signed_lease" label="Bail signe (PDF)" accept=".pdf,.jpg,.jpeg,.png" hint="PDF, JPG ou PNG (max 10 Mo)" :current="$lease->signed_lease_path ? basename($lease->signed_lease_path) : null" />
                        <x-file-upload-multiple name="entry_inspection" label="Etat des lieux d'entree" accept=".jpg,.jpeg,.png,.pdf" hint="PDF, JPG ou PNG (max 10 Mo chacun)" :existing="$lease->entry_inspection_path ?? []" />
                    </div>
                </div>

                {{-- Step 5: Dossier Excel --}}
                <div x-show="currentStep === 4" data-step="4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">N° Appartement</label>
                            <input type="text" name="dossier_number" value="{{ $lease->dossier_number }}" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Agence gerante</label>
                            <input type="text" name="agency_name" value="{{ $lease->agency_name }}" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Date etat des lieux d'entree</label>
                            <input type="date" name="entry_inventory_date" value="{{ $lease->entry_inventory_date?->format('Y-m-d') }}" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Caution 2 mois (FCFA)</label>
                            <x-money-input name="caution_2_mois" :value="$lease->caution_2_mois" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Loyers avances 2 mois (FCFA)</label>
                            <x-money-input name="loyers_avances_2_mois" :value="$lease->loyers_avances_2_mois" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Frais agence (FCFA)</label>
                            <x-money-input name="frais_agence" :value="$lease->frais_agence" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Date depot preavis</label>
                            <input type="date" name="notice_deposit_date" value="{{ $lease->notice_deposit_date?->format('Y-m-d') }}" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Date etat lieux sortie</label>
                            <input type="date" name="exit_inventory_date" value="{{ $lease->exit_inventory_date?->format('Y-m-d') }}" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-secondary">Date de sortie effective</label>
                            <input type="date" name="actual_exit_date" value="{{ $lease->actual_exit_date?->format('Y-m-d') }}" class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                    </div>
                </div>

                {{-- Step 6: Notes --}}
                <div x-show="currentStep === 5" data-step="5">
                    <div>
                        <label class="block text-sm font-medium text-on-surface-secondary">Notes</label>
                        <textarea name="notes" rows="4" placeholder="Notes supplementaires..." class="mt-1.5 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ $lease->notes }}</textarea>
                    </div>
                </div>
            </x-wizard-modal>
        @endif
    @endcan
    @endif

    {{-- Payment Modals for unpaid monthlies --}}
    @if(!$isTerminated && $lease->leaseMonthlies)
        @foreach($lease->leaseMonthlies as $monthly)
            @if($monthly->status !== 'paye')
                @can('create', App\Models\Payment::class)
                    <x-form-modal name="pay-monthly-{{ $monthly->id }}" title="Paiement - {{ $monthly->month }}" :action="route('payments.store')" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>' iconColor="text-green-500">
                        {{-- Monthly info summary --}}
                        <div class="bg-brand-50/60 dark:bg-brand-950/30 rounded-xl border border-brand-100 dark:border-gray-600 p-4 mb-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div>
                                    <p class="text-xs text-brand-500">Locataire</p>
                                    <p class="text-sm font-semibold text-brand-900 dark:text-brand-200">{{ $lease->tenant->full_name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-brand-500">Bien</p>
                                    <p class="text-sm font-semibold text-brand-900 dark:text-brand-200">{{ $lease->property->reference ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-brand-500">Total du</p>
                                    <p class="text-sm font-semibold text-brand-900 dark:text-brand-200">{{ number_format($monthly->total_due, 0, ',', ' ') }} F</p>
                                </div>
                                <div>
                                    <p class="text-xs text-brand-500">Reste a payer</p>
                                    <p class="text-sm font-bold text-red-700 dark:text-red-300">{{ number_format($monthly->remaining_amount, 0, ',', ' ') }} F</p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="lease_monthly_id" value="{{ $monthly->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-on-surface-secondary">Montant (FCFA) <span class="text-red-500">*</span></label>
                                <x-money-input name="amount" :value="$monthly->remaining_amount" :required="true" />
                                <p class="mt-1 text-xs text-on-surface-muted">Maximum : {{ number_format($monthly->remaining_amount, 0, ',', ' ') }} FCFA</p>
                                <template x-if="errors.amount"><p class="mt-1 text-sm text-red-600" x-text="errors.amount[0]"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-on-surface-secondary">Date de paiement <span class="text-red-500">*</span></label>
                                <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <template x-if="errors.paid_at"><p class="mt-1 text-sm text-red-600" x-text="errors.paid_at[0]"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-on-surface-secondary">Methode <span class="text-red-500">*</span></label>
                                <select name="method" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                    <option value="">Selectionner</option>
                                    <option value="especes">Especes</option>
                                    <option value="virement">Virement</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="depot_bancaire">Dépôt bancaire</option>
                                </select>
                                <template x-if="errors.method"><p class="mt-1 text-sm text-red-600" x-text="errors.method[0]"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-on-surface-secondary">Reference</label>
                                <input type="text" name="reference" placeholder="N° recu, N° virement..." class="mt-1.5 h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-on-surface-secondary">Note</label>
                                <textarea name="note" rows="2" placeholder="Commentaire optionnel..." class="mt-1.5 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <x-file-upload name="receipt" label="Justificatif" />
                            </div>
                        </div>
                    </x-form-modal>
                @endcan
            @endif
        @endforeach
    @endif

    {{-- Termination modal --}}
    @if($lease->status === 'actif')
    <x-modal name="terminate-lease" maxWidth="lg">
        <div class="p-6">
            {{-- Header: title + close button --}}
            <div class="flex items-center justify-between mb-0">
                <h2 class="text-lg font-semibold text-on-surface">Resilier le bail</h2>
                <button type="button" x-on:click="$dispatch('close-modal', 'terminate-lease')"
                        class="text-on-surface-faint hover:text-on-surface-secondary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <hr class="my-4 border-theme">

            <p class="text-sm text-on-surface-muted mb-5">Cette action mettra fin au bail. Veuillez renseigner les informations de resiliation.</p>

            <form method="POST" action="{{ route('leases.terminate', $lease) }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-2 gap-x-4 gap-y-5">
                    {{-- Date de sortie --}}
                    <div>
                        <label for="termination_date" class="block text-sm font-medium text-on-surface-secondary mb-1.5">Date de sortie <span class="text-red-500">*</span></label>
                        <input type="date" name="termination_date" id="termination_date" required
                               class="h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    </div>

                    {{-- Placeholder for grid alignment --}}
                    <div></div>

                    {{-- Motif --}}
                    <div class="col-span-2">
                        <label for="termination_reason" class="block text-sm font-medium text-on-surface-secondary mb-1.5">Motif</label>
                        <textarea name="termination_reason" id="termination_reason" rows="3"
                                  class="w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface placeholder:text-on-surface-faint focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden"
                                  placeholder="Motif de la resiliation..."></textarea>
                    </div>

                    {{-- Etat des lieux --}}
                    <div class="col-span-2">
                        <x-file-upload name="exit_inspection" label="Etat des lieux de sortie" accept=".pdf,.jpg,.jpeg,.png" hint="PDF, JPG ou PNG (max 10 Mo)" />
                    </div>
                </div>

                {{-- Submit buttons --}}
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-medium text-white hover:bg-red-700 transition shadow-sm">
                        Confirmer la resiliation
                    </button>
                    <button type="button" x-on:click="$dispatch('close-modal', 'terminate-lease')"
                            class="inline-flex items-center justify-center rounded-lg border border-theme px-5 py-3 text-sm font-medium text-on-surface-secondary bg-surface hover:bg-surface-hover transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
    @endif

@push('scripts')
<script>
function leaseDates(initStart, initEnd) {
    return {
        startDate: initStart || '',
        endDate: initEnd || '',
        duration: '',
        computeDuration() {
            if (!this.startDate || !this.endDate) { this.duration = ''; return; }
            const s = new Date(this.startDate);
            const e = new Date(this.endDate);
            if (e <= s) { this.duration = ''; return; }
            let months = (e.getFullYear() - s.getFullYear()) * 12 + (e.getMonth() - s.getMonth());
            if (e.getDate() < s.getDate()) months--;
            this.duration = Math.max(months, 1);
        },
        onStartChange() {
            if (this.endDate && this.endDate <= this.startDate) {
                this.endDate = '';
            }
        }
    };
}
</script>
@endpush
@endsection
