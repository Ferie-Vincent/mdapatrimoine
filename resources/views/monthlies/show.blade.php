@extends('layouts.app')

@section('title', 'Échéance ' . $monthly->month)

@section('content')
    @php
        $mDotColors = ['paye' => 'bg-emerald-400', 'impaye' => 'bg-red-400', 'partiel' => 'bg-amber-400', 'en_retard' => 'bg-red-400', 'a_venir' => 'bg-blue-400'];
        $mLabels = ['paye' => 'Paye', 'impaye' => 'Impaye', 'partiel' => 'Partiel', 'en_retard' => 'En retard', 'a_venir' => 'A venir'];
    @endphp

    {{-- Hero Header --}}
    <div class="relative bg-gradient-to-br from-brand-700 via-brand-500 to-indigo-500 dark:from-brand-900 dark:via-brand-700 dark:to-indigo-800 rounded-2xl overflow-hidden mb-6 print:hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 44px 44px;"></div>
        <div class="relative flex items-center justify-between px-5 pt-5">
            <a href="{{ route('monthlies.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center p-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
                @can('create', App\Models\Payment::class)
                    @if($monthly->status !== 'paye')
                        <button @click="$dispatch('open-modal', 'pay-monthly-{{ $monthly->id }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-500/80 hover:bg-emerald-500 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Paiement
                        </button>
                    @endif
                @endcan
                @can('create', App\Models\Document::class)
                    @if($monthly->status === 'paye')
                        <form method="POST" action="{{ route('documents.generate-quittance') }}" class="inline">
                            @csrf
                            <input type="hidden" name="lease_monthly_id" value="{{ $monthly->id }}">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                                Quittance
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
        <div class="relative text-center px-6 pb-8 pt-6">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-sm text-white border border-white/20">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $mDotColors[$monthly->status] ?? 'bg-gray-400' }}"></span>
                {{ $mLabels[$monthly->status] ?? ucfirst($monthly->status) }}
            </span>
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-3">Échéance {{ $monthly->month }}</h1>
            <p class="text-white/70 mt-2">Date d'échéance : {{ $monthly->due_date?->format('d/m/Y') }}</p>
            <div class="mt-3 flex items-center justify-center flex-wrap gap-4 text-sm text-white/60">
                @if($monthly->lease?->tenant)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $monthly->lease->tenant->full_name }}
                    </span>
                @endif
                @if($monthly->lease?->property)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ $monthly->lease->property->reference }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-surface rounded-2xl border border-theme-subtle mb-6 overflow-hidden">
        <div class="px-6 py-5 border-b border-theme-subtle bg-surface-hover/50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Locataire</p>
                        @if($monthly->lease && $monthly->lease->tenant)
                            <a href="{{ route('tenants.show', $monthly->lease->tenant) }}" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">{{ $monthly->lease->tenant->full_name }}</a>
                        @else <p class="text-sm text-on-surface-faint">-</p> @endif
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Bien</p>
                        @if($monthly->lease && $monthly->lease->property)
                            <a href="{{ route('properties.show', $monthly->lease->property) }}" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">{{ $monthly->lease->property->reference }}</a>
                            <p class="text-xs text-on-surface-muted mt-0.5">{{ $monthly->lease->property->address }}</p>
                        @else <p class="text-sm text-on-surface-faint">-</p> @endif
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-on-surface-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Bail</p>
                        @if($monthly->lease)
                            <a href="{{ route('leases.show', $monthly->lease) }}" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">Bail #{{ $monthly->lease->id }}</a>
                        @else <p class="text-sm text-on-surface-faint">-</p> @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Detail financier</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-surface-hover rounded-xl p-4 text-center border border-theme-subtle">
                    <p class="text-xs text-on-surface-faint">Loyer du</p>
                    <p class="text-lg font-bold text-on-surface mt-1">{{ number_format($monthly->rent_due, 0, ',', ' ') }} <span class="text-xs text-on-surface-muted">F</span></p>
                </div>
                <div class="bg-orange-50/60 dark:bg-orange-950/30 rounded-xl p-4 text-center border border-orange-100 dark:border-orange-800/30">
                    <p class="text-xs text-orange-500">Penalites</p>
                    <p class="text-lg font-bold text-orange-700 dark:text-orange-300 mt-1">{{ number_format($monthly->penalty_due, 0, ',', ' ') }} <span class="text-xs text-on-surface-muted">F</span></p>
                </div>
                <div class="bg-brand-50/60 dark:bg-brand-950/30 rounded-xl p-4 text-center border border-brand-100 dark:border-gray-600">
                    <p class="text-xs text-brand-500">Total du</p>
                    <p class="text-lg font-bold text-brand-700 dark:text-brand-300 mt-1">{{ number_format($monthly->total_due, 0, ',', ' ') }} <span class="text-xs text-on-surface-muted">F</span></p>
                </div>
                <div class="bg-emerald-50/60 dark:bg-emerald-950/30 rounded-xl p-4 text-center border border-emerald-100 dark:border-emerald-800/30">
                    <p class="text-xs text-emerald-500">Paye</p>
                    <p class="text-lg font-bold text-emerald-700 dark:text-emerald-300 mt-1">{{ number_format($monthly->paid_amount, 0, ',', ' ') }} <span class="text-xs text-on-surface-muted">F</span></p>
                </div>
                <div class="bg-red-50/60 dark:bg-red-950/30 rounded-xl p-4 text-center border border-red-100 dark:border-red-800/30">
                    <p class="text-xs text-red-500">Reste</p>
                    <p class="text-lg font-bold text-red-700 dark:text-red-300 mt-1">{{ number_format($monthly->remaining_amount, 0, ',', ' ') }} <span class="text-xs text-on-surface-muted">F</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Payments table --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden">
        <div class="px-6 py-4 border-b border-theme-subtle">
            <h3 class="text-lg font-medium text-on-surface">Paiements enregistrés</h3>
        </div>
        @if($monthly->payments && $monthly->payments->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme-subtle">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Date</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Montant</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Méthode</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Référence</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Note</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Enregistré par</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme-subtle">
                        @foreach($monthly->payments as $payment)
                            <tr class="hover:bg-surface-hover/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $payment->paid_at?->format('d/m/Y') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-accent-green-500 text-right">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ ucfirst($payment->method ?? '-') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $payment->reference ?? '-' }}</td>
                                <td class="px-6 py-5 text-sm text-on-surface-secondary max-w-xs truncate">{{ $payment->note ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $payment->recorder->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-empty-state message="Aucun paiement enregistré pour cette échéance." />
        @endif
    </div>

    {{-- Payment Modal --}}
    @if($monthly->status !== 'paye')
        @can('create', App\Models\Payment::class)
            <x-form-modal name="pay-monthly-{{ $monthly->id }}" title="Paiement - {{ $monthly->month }}" :action="route('payments.store')" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>' iconColor="text-green-500">
                {{-- Monthly info summary --}}
                <div class="bg-brand-50/60 dark:bg-brand-950/30 rounded-xl border border-brand-100 dark:border-gray-600 p-4 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <p class="text-xs text-brand-500">Locataire</p>
                            <p class="text-sm font-semibold text-brand-900 dark:text-brand-200">{{ $monthly->lease->tenant->full_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-brand-500">Bien</p>
                            <p class="text-sm font-semibold text-brand-900 dark:text-brand-200">{{ $monthly->lease->property->reference ?? '-' }}</p>
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
@endsection
