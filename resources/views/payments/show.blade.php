@extends('layouts.app')

@section('title', 'Paiement #' . $payment->id)

@section('actions')
    <div class="flex items-center gap-1.5">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimer
        </button>
        @can('create', App\Models\Document::class)
            <form method="POST" action="{{ route('documents.generate-receipt') }}" class="inline">
                @csrf
                <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Générer reçu
                </button>
            </form>
        @endcan
    </div>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('payments.index') }}" class="text-sm text-brand-600 hover:text-brand-900">&larr; Retour aux paiements</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 mb-6 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-slate-800 to-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Paiement #{{ $payment->id }}</h3>
                    <p class="text-sm text-slate-300">{{ $payment->paid_at?->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="bg-emerald-400/20 text-emerald-300 px-4 py-2 rounded-xl">
                <p class="text-xl font-bold">{{ number_format($payment->amount, 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
            </div>
        </div>

        <div class="px-6 py-5 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Details</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Methode</p>
                        @php $methodLabels = ['virement' => 'Virement', 'especes' => 'Especes', 'cheque' => 'Cheque', 'mobile_money' => 'Mobile Money', 'versement_especes' => 'Versement especes sur compte', 'depot_bancaire' => 'Depot bancaire']; @endphp
                        <p class="text-sm font-semibold text-gray-900">{{ $methodLabels[$payment->method] ?? ucfirst($payment->method ?? '-') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Reference</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $payment->reference ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Enregistre par</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $payment->recorder->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($payment->note)
        <div class="px-6 py-4 border-b border-gray-100 bg-slate-50/30">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Note</p>
            <p class="text-sm text-gray-700">{{ $payment->note }}</p>
        </div>
        @endif

        @if($payment->receipt_path)
        <div class="px-6 py-4">
            <a href="{{ asset('storage/' . $payment->receipt_path) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                Voir le justificatif
            </a>
        </div>
        @endif
    </div>

    @if($payment->leaseMonthly)
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900">Echeance associee &mdash; {{ $payment->leaseMonthly->month }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Locataire</p>
                        @if($payment->leaseMonthly->lease && $payment->leaseMonthly->lease->tenant)
                            <a href="{{ route('tenants.show', $payment->leaseMonthly->lease->tenant) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-800">{{ $payment->leaseMonthly->lease->tenant->full_name }}</a>
                        @else <p class="text-sm text-gray-400">-</p> @endif
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Bien</p>
                        @if($payment->leaseMonthly->lease && $payment->leaseMonthly->lease->property)
                            <a href="{{ route('properties.show', $payment->leaseMonthly->lease->property) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-800">{{ $payment->leaseMonthly->lease->property->reference }}</a>
                        @else <p class="text-sm text-gray-400">-</p> @endif
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-brand-50/60 rounded-xl p-4 border border-brand-100 text-center">
                    <p class="text-xs text-brand-500">Total du</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($payment->leaseMonthly->total_due, 0, ',', ' ') }} <span class="text-xs text-gray-500">F</span></p>
                </div>
                <div class="bg-emerald-50/60 rounded-xl p-4 border border-emerald-100 text-center">
                    <p class="text-xs text-emerald-500">Paye</p>
                    <p class="text-lg font-bold text-emerald-700">{{ number_format($payment->leaseMonthly->paid_amount, 0, ',', ' ') }} <span class="text-xs text-gray-500">F</span></p>
                </div>
                <div class="bg-red-50/60 rounded-xl p-4 border border-red-100 text-center">
                    <p class="text-xs text-red-500">Reste</p>
                    <p class="text-lg font-bold text-red-700">{{ number_format($payment->leaseMonthly->remaining_amount, 0, ',', ' ') }} <span class="text-xs text-gray-500">F</span></p>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
