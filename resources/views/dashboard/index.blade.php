@extends('layouts.app')

@section('title', 'Tableau de bord')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')

    @php
        $monthly = $chartData['monthly_trend'] ?? [];
        $lastMonth = $monthly[count($monthly) - 2] ?? null;
        $currentMonthData = $monthly[count($monthly) - 1] ?? null;

        // Trend calculations
        $prevExpected = $lastMonth['expected'] ?? 0;
        $prevCollected = $lastMonth['collected'] ?? 0;
        $prevUnpaid = $lastMonth['unpaid'] ?? 0;

        $trendExpected = $prevExpected > 0 ? round((($stats['total_expected'] - $prevExpected) / $prevExpected) * 100, 1) : 0;
        $trendCollected = $prevCollected > 0 ? round((($stats['total_collected'] - $prevCollected) / $prevCollected) * 100, 1) : 0;

        $unpaidTrendLast = ($chartData['unpaid_trend'][count($chartData['unpaid_trend']) - 1] ?? 0);
        $unpaidTrendPrev = ($chartData['unpaid_trend'][count($chartData['unpaid_trend']) - 2] ?? 0);
        $unpaidDiff = round($unpaidTrendLast - $unpaidTrendPrev, 1);
    @endphp

    {{-- Overview Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Vue d'ensemble</h2>
    </div>

    {{-- Vue globale - Row 1: Financier --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
        <x-stat-card
            title="Total attendu"
            :value="number_format($stats['total_expected'], 0, ',', ' ') . ' FCFA'"
            color="blue"
            trend="up"
            :trendValue="'+' . abs($trendExpected) . '%'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        />
        <x-stat-card
            title="Total encaisse"
            :value="number_format($stats['total_collected'], 0, ',', ' ') . ' FCFA'"
            color="green"
            trend="up"
            :trendValue="'+' . abs($trendCollected) . '%'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        />
        <x-stat-card
            title="Total impaye"
            :value="number_format($stats['total_unpaid'], 0, ',', ' ') . ' FCFA'"
            color="red"
            trend="{{ $stats['total_unpaid'] > 0 ? 'down' : 'up' }}"
            :trendValue="number_format($stats['total_unpaid'], 0, ',', ' ') . ' F'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>'
        />
        <x-stat-card
            title="A venir"
            :value="number_format($stats['total_upcoming'], 0, ',', ' ') . ' FCFA'"
            color="blue"
            :subtitle="$stats['upcoming_count'] . ' echeance(s)'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'
        />
        <x-stat-card
            title="Taux recouvrement"
            :value="number_format($stats['recovery_rate'], 1, ',', ' ') . ' %'"
            color="orange"
            trend="{{ $stats['recovery_rate'] >= 80 ? 'up' : 'down' }}"
            :trendValue="number_format($stats['recovery_rate'], 1) . '%'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'
        />
    </div>

    {{-- Vue globale - Row 2: Patrimoine --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card title="Biens" :value="(string) $stats['properties_count']" subtitle="Total des biens immobiliers"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'
        />
        <x-stat-card title="Biens occupes" :value="(string) $stats['occupied_count']" color="green" subtitle="{{ $stats['properties_count'] > 0 ? round($stats['occupied_count'] / $stats['properties_count'] * 100) . '% du parc' : '—' }}"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'
        />
        <x-stat-card title="Biens vacants" :value="(string) $stats['vacant_count']" color="red" subtitle="{{ $stats['properties_count'] > 0 ? round($stats['vacant_count'] / $stats['properties_count'] * 100) . '% du parc' : '—' }}"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>'
        />
        <x-stat-card title="Baux actifs" :value="(string) $stats['active_leases_count']" color="yellow" subtitle="{{ $chartData['new_leases_month'] > 0 ? '+' . $chartData['new_leases_month'] . ' ce mois' : 'Aucun nouveau ce mois' }}"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
        />
    </div>

    {{-- Mois en cours --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Mois en cours ({{ $monthStats['label'] }})</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card title="Attendu ce mois" :value="number_format($monthStats['expected'], 0, ',', ' ') . ' FCFA'" color="blue" />
        <x-stat-card title="Encaisse ce mois" :value="number_format($monthStats['collected'], 0, ',', ' ') . ' FCFA'" color="green" />
        <x-stat-card title="Impaye ce mois" :value="number_format($monthStats['unpaid'], 0, ',', ' ') . ' FCFA'" color="red" />
        <x-stat-card title="Taux ce mois" :value="number_format($monthStats['recovery_rate'], 1, ',', ' ') . ' %'" color="orange" />
    </div>

    {{-- Secondary metric cards row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        {{-- Taux d'impaye with sparkline --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Taux d'impaye</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $unpaidTrendLast }}%</p>
                    <p class="text-xs mt-1 {{ $unpaidDiff <= 0 ? 'text-accent-green-400' : 'text-accent-red-400' }}">
                        {{ $unpaidDiff >= 0 ? '+' : '' }}{{ $unpaidDiff }}% vs mois dernier
                    </p>
                </div>
                <div id="sparkline-unpaid" class="w-28 h-16"></div>
            </div>
        </div>

        {{-- Croissance locative with sparkline --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Croissance locative</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $chartData['active_leases'] }} baux</p>
                    <p class="text-xs mt-1 text-accent-green-400">
                        +{{ $chartData['new_leases_month'] }} ce mois
                    </p>
                </div>
                <div id="sparkline-leases" class="w-28 h-16"></div>
            </div>
        </div>
    </div>

    {{-- Charts row: Area chart + Donut chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        {{-- Area chart (2/3) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Encaissements mensuels</h3>
            <div id="chart-monthly" class="w-full" style="min-height: 320px;"></div>
        </div>

        {{-- Donut chart (1/3) --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Occupation des biens</h3>
            <div id="chart-occupation" class="w-full" style="min-height: 320px;"></div>
        </div>
    </div>

    {{-- Map: Localisation des biens --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Localisation des biens</h3>
        <div id="map-properties" style="height:400px; position:relative; z-index:0;" class="rounded-xl"></div>
        @if($mapProperties->isEmpty())
            <p class="text-sm text-gray-400 text-center mt-3">Aucun bien avec coordonnees GPS. Editez un bien pour ajouter sa latitude / longitude.</p>
        @endif
    </div>

    {{-- Evolution charts row: Recovery rate + Revenue evolution --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Taux de recouvrement (12 mois)</h3>
            <div id="chart-recovery-rate" class="w-full" style="min-height: 320px;"></div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Evolution des revenus</h3>
            <div id="chart-revenue-evolution" class="w-full" style="min-height: 320px;"></div>
        </div>
    </div>

    {{-- Projection chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Projection de revenus</h3>
            <span class="text-xs font-medium text-gray-400 bg-gray-100 rounded-full px-3 py-1">6 mois historique + 6 mois projection</span>
        </div>
        <div id="chart-projection" class="w-full" style="min-height: 340px;"></div>
    </div>

    {{-- Expiring contracts alert --}}
    @if(($expiringContractsCount ?? 0) > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <p class="text-sm text-amber-700">
                <strong>{{ $expiringContractsCount }}</strong> contrat(s) fournisseur(s) expire(nt) dans les 30 prochains jours.
                <a href="{{ route('service-providers.index') }}" class="underline font-medium hover:text-amber-900">Voir les contrats</a>
            </p>
        </div>
    @endif

    {{-- Bottom row: Overdue table + Recent activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Overdue table (2/3) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent-red-400 to-accent-red-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Echeances en retard</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $overdueMonthlies->count() }} echeance(s) en attente de paiement</p>
                    </div>
                </div>
                @if($overdueMonthlies->count())
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-accent-red-50 text-accent-red-400">
                        {{ $overdueMonthlies->count() }}
                    </span>
                @endif
            </div>

            @if($overdueMonthlies->count())
                <div class="divide-y divide-gray-50">
                    @foreach($overdueMonthlies->take(8) as $monthly)
                        <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/60 transition group">
                            {{-- Avatar --}}
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shrink-0 shadow-sm">
                                <span class="text-xs font-bold text-white">{{ mb_substr($monthly->lease->tenant->last_name ?? '?', 0, 1) }}{{ mb_substr($monthly->lease->tenant->first_name ?? '', 0, 1) }}</span>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $monthly->lease->tenant->full_name ?? '-' }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $monthly->month }}
                                    </span>
                                    @if($monthly->lease->property ?? null)
                                        <span class="text-gray-300">&middot;</span>
                                        <span class="text-xs text-gray-400 truncate">{{ $monthly->lease->property->reference ?? '' }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Amount --}}
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-accent-red-400">{{ number_format($monthly->remaining_amount, 0, ',', ' ') }} F</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">reste a payer</p>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 shrink-0 ml-2">
                                <a href="{{ route('monthlies.show', $monthly) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-500 bg-gray-50 rounded-lg hover:bg-gray-100 hover:text-gray-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Voir
                                </a>
                                @can('create', App\Models\Payment::class)
                                    <button @click="$dispatch('open-modal', 'pay-monthly-{{ $monthly->id }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-accent-green-400 rounded-lg hover:bg-accent-green-500 transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                        Payer
                                    </button>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state message="Aucune echeance en retard. Tout est a jour !" />
            @endif
        </div>

        {{-- Recent activity (1/3) --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Activite recente</h3>
            <div class="space-y-4">
                @foreach(($chartData['recent_payments'] ?? []) as $payment)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-accent-green-50 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-accent-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900">Paiement de {{ number_format($payment['amount'], 0, ',', ' ') }} F</p>
                            <p class="text-xs text-gray-500">{{ $payment['tenant'] }} &middot; {{ $payment['paid_at'] }}</p>
                        </div>
                    </div>
                @endforeach

                @foreach(($chartData['recent_leases'] ?? []) as $lease)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-50 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900">Bail {{ $lease['status'] }}</p>
                            <p class="text-xs text-gray-500">{{ $lease['tenant'] }} &middot; {{ $lease['property'] }} &middot; {{ $lease['date'] }}</p>
                        </div>
                    </div>
                @endforeach

                @if(empty($chartData['recent_payments']) && empty($chartData['recent_leases']))
                    <p class="text-sm text-gray-400 text-center py-4">Aucune activite recente</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment Modals for overdue monthlies --}}
    @if(isset($overdueMonthlies))
        @foreach($overdueMonthlies->take(8) as $monthly)
            @if($monthly->status !== 'paye')
                @can('create', App\Models\Payment::class)
                    <x-form-modal name="pay-monthly-{{ $monthly->id }}" title="Paiement - {{ $monthly->month }}" :action="route('payments.store')" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>' iconColor="text-green-500">
                        <div class="bg-brand-50/60 rounded-xl border border-brand-100 p-4 mb-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div>
                                    <p class="text-xs text-brand-500">Locataire</p>
                                    <p class="text-sm font-semibold text-brand-900">{{ $monthly->lease->tenant->full_name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-brand-500">Bien</p>
                                    <p class="text-sm font-semibold text-brand-900">{{ $monthly->lease->property->reference ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-brand-500">Total du</p>
                                    <p class="text-sm font-semibold text-brand-900">{{ number_format($monthly->total_due, 0, ',', ' ') }} F</p>
                                </div>
                                <div>
                                    <p class="text-xs text-brand-500">Reste a payer</p>
                                    <p class="text-sm font-bold text-red-700">{{ number_format($monthly->remaining_amount, 0, ',', ' ') }} F</p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="lease_monthly_id" value="{{ $monthly->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Montant (FCFA) <span class="text-red-500">*</span></label>
                                <x-money-input name="amount" :value="$monthly->remaining_amount" :required="true" />
                                <template x-if="errors.amount"><p class="mt-1 text-sm text-red-600" x-text="errors.amount[0]"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date de paiement <span class="text-red-500">*</span></label>
                                <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <template x-if="errors.paid_at"><p class="mt-1 text-sm text-red-600" x-text="errors.paid_at[0]"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Methode <span class="text-red-500">*</span></label>
                                <select name="method" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                    <option value="">Selectionner</option>
                                    <option value="especes">Especes</option>
                                    <option value="virement">Virement</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                                <template x-if="errors.method"><p class="mt-1 text-sm text-red-600" x-text="errors.method[0]"></p></template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Reference</label>
                                <input type="text" name="reference" placeholder="N° recu, N° virement..." class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Note</label>
                                <textarea name="note" rows="2" placeholder="Commentaire optionnel..." class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden"></textarea>
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

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Leaflet Map: Localisation des biens ──
    const mapEl = document.getElementById('map-properties');
    if (mapEl) {
        const properties = @json($mapProperties);
        const defaultCenter = [6.8, -5.55];
        const defaultZoom = 7;

        let center = defaultCenter;
        let zoom = defaultZoom;
        if (properties.length === 1) {
            center = [properties[0].latitude, properties[0].longitude];
            zoom = 14;
        }

        const map = L.map('map-properties').setView(center, zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(map);

        const statusLabels = { disponible: 'Disponible', occupe: 'Occupé', travaux: 'En travaux' };
        const bounds = [];
        properties.forEach(function(prop) {
            const marker = L.marker([prop.latitude, prop.longitude]).addTo(map);
            marker.bindPopup(
                '<strong>' + prop.reference + '</strong>' +
                '<br><span style="text-transform:capitalize;">' + (prop.type || '') + '</span> — ' + (statusLabels[prop.status] || prop.status) +
                (prop.address ? '<br>' + prop.address : '') +
                '<br><a href="https://www.google.com/maps/dir/?api=1&destination=' + prop.latitude + ',' + prop.longitude + '" target="_blank" rel="noopener" style="color:#1E3A8A;font-weight:600;">Itinéraire &rarr;</a>'
            );
            bounds.push([prop.latitude, prop.longitude]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }
    }
    const chartData = @json($chartData);

    // ── Area Chart: Encaissements mensuels ──
    const monthlyTrend = chartData.monthly_trend || [];
    new ApexCharts(document.querySelector('#chart-monthly'), {
        chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
        series: [
            { name: 'Attendu', data: monthlyTrend.map(m => m.expected) },
            { name: 'Encaisse', data: monthlyTrend.map(m => m.collected) },
        ],
        xaxis: {
            categories: monthlyTrend.map(m => m.month),
            labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        yaxis: {
            labels: {
                formatter: v => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v),
                style: { fontSize: '11px', colors: '#9ca3af' },
            },
        },
        colors: ['#1E3A8A', '#1E9B3E'],
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] },
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
        tooltip: {
            y: { formatter: v => new Intl.NumberFormat('fr-FR').format(v) + ' F' },
        },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
    }).render();

    // ── Donut Chart: Occupation ──
    const propStatus = chartData.property_status || [];
    const statusLabels = { occupe: 'Occupe', disponible: 'Disponible', travaux: 'Travaux' };
    const statusColors = { occupe: '#1E9B3E', disponible: '#D4812A', travaux: '#1E3A8A' };
    new ApexCharts(document.querySelector('#chart-occupation'), {
        chart: { type: 'donut', height: 320, fontFamily: 'Outfit, sans-serif' },
        series: propStatus.map(s => s.count),
        labels: propStatus.map(s => statusLabels[s.status] || s.status),
        colors: propStatus.map(s => statusColors[s.status] || '#9ca3af'),
        legend: { position: 'bottom', fontSize: '12px' },
        dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%' },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '14px',
                            color: '#6b7280',
                        },
                    },
                },
            },
        },
    }).render();

    // ── Line Chart: Taux de recouvrement 12 mois ──
    new ApexCharts(document.querySelector('#chart-recovery-rate'), {
        chart: { type: 'line', height: 320, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
        series: [{
            name: 'Taux de recouvrement',
            data: monthlyTrend.map(m => m.expected > 0 ? Math.round((m.collected / m.expected) * 1000) / 10 : 0)
        }],
        xaxis: {
            categories: monthlyTrend.map(m => m.month),
            labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        yaxis: {
            max: 100, min: 0,
            labels: { formatter: v => v.toFixed(0) + '%', style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        colors: ['#D4812A'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4, strokeWidth: 0 },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: v => v.toFixed(1) + ' %' } },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        annotations: {
            yaxis: [{ y: 80, borderColor: '#1E9B3E', strokeDashArray: 4, label: { text: 'Objectif 80%', style: { fontSize: '10px', color: '#1E9B3E', background: 'transparent' } } }]
        },
    }).render();

    // ── Bar Chart: Evolution des revenus ──
    new ApexCharts(document.querySelector('#chart-revenue-evolution'), {
        chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
        series: [
            { name: 'Attendu', data: monthlyTrend.map(m => m.expected) },
            { name: 'Encaisse', data: monthlyTrend.map(m => m.collected) },
        ],
        xaxis: {
            categories: monthlyTrend.map(m => m.month),
            labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        yaxis: {
            labels: {
                formatter: v => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v),
                style: { fontSize: '11px', colors: '#9ca3af' },
            },
        },
        colors: ['#1E3A8A', '#1E9B3E'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: v => new Intl.NumberFormat('fr-FR').format(v) + ' F' } },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
    }).render();

    // ── Line Chart: Projection de revenus ──
    const projectionData = @json($projection);
    const projHistorical = projectionData.historical || [];
    const projProjected = projectionData.projected || [];
    const projLabels = projHistorical.map(h => h.month).concat(projProjected.map(p => p.month));
    const historicalValues = projHistorical.map(h => h.collected);
    const projectedValues = new Array(projHistorical.length - 1).fill(null)
        .concat([projHistorical[projHistorical.length - 1]?.collected ?? 0])
        .concat(projProjected.map(p => p.projected));

    new ApexCharts(document.querySelector('#chart-projection'), {
        chart: { type: 'line', height: 340, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
        series: [
            { name: 'Historique encaisse', data: historicalValues.concat(new Array(projProjected.length).fill(null)) },
            { name: 'Projection', data: projectedValues },
        ],
        xaxis: {
            categories: projLabels,
            labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        yaxis: {
            labels: {
                formatter: v => v !== null ? new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v) : '',
                style: { fontSize: '11px', colors: '#9ca3af' },
            },
        },
        colors: ['#1E9B3E', '#D4812A'],
        stroke: { curve: 'smooth', width: [3, 3], dashArray: [0, 5] },
        markers: { size: [4, 4], strokeWidth: 0 },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: v => v !== null ? new Intl.NumberFormat('fr-FR').format(v) + ' F' : '-' } },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
    }).render();

    // ── Sparkline: Taux d'impaye ──
    new ApexCharts(document.querySelector('#sparkline-unpaid'), {
        chart: { type: 'area', height: 64, sparkline: { enabled: true } },
        series: [{ data: chartData.unpaid_trend || [] }],
        colors: ['#C42618'],
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] },
        },
        stroke: { curve: 'smooth', width: 2 },
        tooltip: { enabled: false },
    }).render();

    // ── Sparkline: Croissance locative ──
    new ApexCharts(document.querySelector('#sparkline-leases'), {
        chart: { type: 'area', height: 64, sparkline: { enabled: true } },
        series: [{ data: chartData.lease_growth || [] }],
        colors: ['#1E9B3E'],
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] },
        },
        stroke: { curve: 'smooth', width: 2 },
        tooltip: { enabled: false },
    }).render();
});
</script>
@endpush
