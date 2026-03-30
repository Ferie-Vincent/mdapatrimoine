@extends('layouts.app')

@section('title', 'Paiement #' . $payment->id)

@section('content')
    {{-- Hero Header --}}
    <div class="relative bg-gradient-to-br from-brand-700 via-brand-500 to-indigo-500 dark:from-brand-900 dark:via-brand-700 dark:to-indigo-800 rounded-2xl overflow-hidden mb-6 print:hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 44px 44px;"></div>
        <div class="relative flex items-center justify-between px-5 pt-5">
            <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center p-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
                @can('create', App\Models\Document::class)
                    <form method="POST" action="{{ route('documents.generate-receipt') }}" class="inline">
                        @csrf
                        <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Reçu
                        </button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="relative text-center px-6 pb-8 pt-6">
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-emerald-400/20 backdrop-blur-sm text-emerald-300 border border-emerald-400/30">
                {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
            </span>
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-3">Paiement #{{ $payment->id }}</h1>
            <p class="text-white/70 mt-2">{{ $payment->paid_at?->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="bg-surface rounded-2xl border border-theme-subtle mb-6 overflow-hidden">
        <div class="px-6 py-5 border-b border-theme-subtle">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Details</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Methode</p>
                        @php $methodLabels = ['virement' => 'Virement', 'especes' => 'Especes', 'cheque' => 'Cheque', 'mobile_money' => 'Mobile Money', 'depot_bancaire' => 'Dépôt bancaire', 'versement_especes' => 'Versement especes sur compte', 'autre' => 'Autre']; @endphp
                        <p class="text-sm font-semibold text-on-surface">{{ $methodLabels[$payment->method] ?? ucfirst($payment->method ?? '-') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Reference</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $payment->reference ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Enregistre par</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $payment->recorder->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($payment->note)
        <div class="px-6 py-4 border-b border-theme-subtle bg-surface-hover/30">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-2">Note</p>
            <p class="text-sm text-on-surface-secondary">{{ $payment->note }}</p>
        </div>
        @endif

        @if($payment->receipt_path)
        <div class="px-6 py-4">
            <a href="{{ asset('storage/' . $payment->receipt_path) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                Voir le justificatif
            </a>
        </div>
        @endif
    </div>

    @if($payment->leaseMonthly)
    <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden">
        <div class="px-6 py-4 border-b border-theme-subtle flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center">
                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-on-surface">Echeance associee &mdash; {{ $payment->leaseMonthly->month }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Locataire</p>
                        @if($payment->leaseMonthly->lease && $payment->leaseMonthly->lease->tenant)
                            <a href="{{ route('tenants.show', $payment->leaseMonthly->lease->tenant) }}" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">{{ $payment->leaseMonthly->lease->tenant->full_name }}</a>
                        @else <p class="text-sm text-on-surface-faint">-</p> @endif
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Bien</p>
                        @if($payment->leaseMonthly->lease && $payment->leaseMonthly->lease->property)
                            <a href="{{ route('properties.show', $payment->leaseMonthly->lease->property) }}" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">{{ $payment->leaseMonthly->lease->property->reference }}</a>
                        @else <p class="text-sm text-on-surface-faint">-</p> @endif
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-brand-50/60 dark:bg-brand-950/30 rounded-xl p-4 border border-brand-100 dark:border-gray-600 text-center">
                    <p class="text-xs text-brand-500">Total du</p>
                    <p class="text-lg font-bold text-on-surface">{{ number_format($payment->leaseMonthly->total_due, 0, ',', ' ') }} <span class="text-xs text-on-surface-muted">F</span></p>
                </div>
                <div class="bg-emerald-50/60 dark:bg-emerald-950/30 rounded-xl p-4 border border-emerald-100 dark:border-emerald-800/30 text-center">
                    <p class="text-xs text-emerald-500">Paye</p>
                    <p class="text-lg font-bold text-emerald-700 dark:text-emerald-300">{{ number_format($payment->leaseMonthly->paid_amount, 0, ',', ' ') }} <span class="text-xs text-on-surface-muted">F</span></p>
                </div>
                <div class="bg-red-50/60 dark:bg-red-950/30 rounded-xl p-4 border border-red-100 dark:border-red-800/30 text-center">
                    <p class="text-xs text-red-500">Reste</p>
                    <p class="text-lg font-bold text-red-700 dark:text-red-300">{{ number_format($payment->leaseMonthly->remaining_amount, 0, ',', ' ') }} <span class="text-xs text-on-surface-muted">F</span></p>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
