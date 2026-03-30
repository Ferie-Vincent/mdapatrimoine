@extends('layouts.app')

@section('title', 'Paiements')

@section('actions')
    <div class="flex items-center gap-1.5">
        <x-export-dropdown route="exports.payments" :query="request()->query()" />
    </div>
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('payments.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="month" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Période</label>
                <input type="month" name="month" id="month" value="{{ request('month') }}"
                       class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
            </div>
            <div>
                <label for="method" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Méthode</label>
                <select name="method" id="method" class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Toutes</option>
                    <option value="especes" {{ request('method') === 'especes' ? 'selected' : '' }}>Espèces</option>
                    <option value="virement" {{ request('method') === 'virement' ? 'selected' : '' }}>Virement</option>
                    <option value="cheque" {{ request('method') === 'cheque' ? 'selected' : '' }}>Chèque</option>
                    <option value="mobile_money" {{ request('method') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    <option value="depot_bancaire" {{ request('method') === 'depot_bancaire' ? 'selected' : '' }}>Dépôt bancaire</option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-on-surface-faint"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Locataire, référence..."
                           class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface placeholder-on-surface-faint hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all">
                </div>
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle shadow-sm overflow-hidden mt-6">
        @if($payments->count())
            <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full divide-y divide-theme-subtle">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Date</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Montant</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Méthode</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Locataire</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Bien</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Mois</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Référence</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme-subtle">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-surface-hover/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $payment->paid_at?->format('d/m/Y') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-accent-green-500 text-right">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ ucfirst($payment->method ?? '-') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-md bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shrink-0">
                                            <span class="text-white font-semibold text-[10px]">{{ strtoupper(substr($payment->leaseMonthly->lease->tenant->first_name ?? '', 0, 1) . substr($payment->leaseMonthly->lease->tenant->last_name ?? '', 0, 1)) }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-on-surface">{{ $payment->leaseMonthly->lease->tenant->full_name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $payment->leaseMonthly->lease->property->reference ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $payment->leaseMonthly->month ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $payment->reference ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('payments.show', $payment) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-on-surface-secondary bg-surface-hover border border-theme rounded-lg hover:bg-surface-alt transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Voir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-theme-subtle">
                {{ $payments->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state message="Aucun paiement trouvé." />
        @endif
    </div>

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
@endpush
@endsection
