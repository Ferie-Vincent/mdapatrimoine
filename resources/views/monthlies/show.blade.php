@extends('layouts.app')

@section('title', 'Échéance ' . $monthly->month)

@section('actions')
    <div class="flex items-center gap-1.5">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimer
        </button>
        @can('create', App\Models\Payment::class)
            @if($monthly->status !== 'paye')
                <button @click="$dispatch('open-modal', 'pay-monthly-{{ $monthly->id }}')"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 rounded-lg text-xs font-semibold text-white hover:bg-green-700 transition shadow-sm">
                    Enregistrer un paiement
                </button>
            @endif
        @endcan
        @can('create', App\Models\Document::class)
            @if($monthly->status === 'paye')
                <form method="POST" action="{{ route('documents.generate-quittance') }}" class="inline">
                    @csrf
                    <input type="hidden" name="lease_monthly_id" value="{{ $monthly->id }}">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
                        Générer quittance
                    </button>
                </form>
            @endif
        @endcan
        @can('create', App\Models\Document::class)
            <form method="POST" action="{{ route('documents.generate-notice') }}" class="inline">
                @csrf
                <input type="hidden" name="lease_monthly_id" value="{{ $monthly->id }}">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                    Avis d'échéance
                </button>
            </form>
        @endcan
        @can('create', App\Models\Reminder::class)
            @if(in_array($monthly->status, ['impaye', 'partiel', 'en_retard']))
                <form method="POST" action="{{ route('reminders.store') }}" class="inline">
                    @csrf
                    <input type="hidden" name="lease_monthly_id" value="{{ $monthly->id }}">
                    <input type="hidden" name="channel" value="email">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 rounded-lg text-xs font-semibold text-white hover:bg-amber-700 transition shadow-sm">
                        Envoyer relance
                    </button>
                </form>
            @endif
        @endcan
    </div>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('monthlies.index') }}" class="text-sm text-brand-600 hover:text-brand-900">&larr; Retour aux echeances</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 mb-6 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-slate-800 to-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Echeance {{ $monthly->month }}</h3>
                    <p class="text-sm text-slate-300">Date d'echeance : {{ $monthly->due_date?->format('d/m/Y') }}</p>
                </div>
            </div>
            @php
                $mStatusColors = ['paye' => 'bg-emerald-400/20 text-emerald-300 ring-emerald-400/30', 'impaye' => 'bg-red-400/20 text-red-300 ring-red-400/30', 'partiel' => 'bg-amber-400/20 text-amber-300 ring-amber-400/30', 'en_retard' => 'bg-red-400/20 text-red-300 ring-red-400/30'];
                $mDotColors = ['paye' => 'bg-emerald-400', 'impaye' => 'bg-red-400', 'partiel' => 'bg-amber-400', 'en_retard' => 'bg-red-400'];
                $mLabels = ['paye' => 'Paye', 'impaye' => 'Impaye', 'partiel' => 'Partiel', 'en_retard' => 'En retard'];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ring-1 {{ $mStatusColors[$monthly->status] ?? '' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $mDotColors[$monthly->status] ?? 'bg-gray-400' }}"></span>
                {{ $mLabels[$monthly->status] ?? ucfirst($monthly->status) }}
            </span>
        </div>

        <div class="px-6 py-5 border-b border-gray-100 bg-slate-50/50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Locataire</p>
                        @if($monthly->lease && $monthly->lease->tenant)
                            <a href="{{ route('tenants.show', $monthly->lease->tenant) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-800">{{ $monthly->lease->tenant->full_name }}</a>
                        @else <p class="text-sm text-gray-400">-</p> @endif
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Bien</p>
                        @if($monthly->lease && $monthly->lease->property)
                            <a href="{{ route('properties.show', $monthly->lease->property) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-800">{{ $monthly->lease->property->reference }}</a>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $monthly->lease->property->address }}</p>
                        @else <p class="text-sm text-gray-400">-</p> @endif
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Bail</p>
                        @if($monthly->lease)
                            <a href="{{ route('leases.show', $monthly->lease) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-800">Bail #{{ $monthly->lease->id }}</a>
                        @else <p class="text-sm text-gray-400">-</p> @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Detail financier</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                    <p class="text-xs text-gray-400">Loyer du</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($monthly->rent_due, 0, ',', ' ') }} <span class="text-xs text-gray-500">F</span></p>
                </div>
                <div class="bg-orange-50/60 rounded-xl p-4 text-center border border-orange-100">
                    <p class="text-xs text-orange-500">Penalites</p>
                    <p class="text-lg font-bold text-orange-700 mt-1">{{ number_format($monthly->penalty_due, 0, ',', ' ') }} <span class="text-xs text-gray-500">F</span></p>
                </div>
                <div class="bg-brand-50/60 rounded-xl p-4 text-center border border-brand-100">
                    <p class="text-xs text-brand-500">Total du</p>
                    <p class="text-lg font-bold text-brand-700 mt-1">{{ number_format($monthly->total_due, 0, ',', ' ') }} <span class="text-xs text-gray-500">F</span></p>
                </div>
                <div class="bg-emerald-50/60 rounded-xl p-4 text-center border border-emerald-100">
                    <p class="text-xs text-emerald-500">Paye</p>
                    <p class="text-lg font-bold text-emerald-700 mt-1">{{ number_format($monthly->paid_amount, 0, ',', ' ') }} <span class="text-xs text-gray-500">F</span></p>
                </div>
                <div class="bg-red-50/60 rounded-xl p-4 text-center border border-red-100">
                    <p class="text-xs text-red-500">Reste</p>
                    <p class="text-lg font-bold text-red-700 mt-1">{{ number_format($monthly->remaining_amount, 0, ',', ' ') }} <span class="text-xs text-gray-500">F</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Payments table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-medium text-gray-900">Paiements enregistrés</h3>
        </div>
        @if($monthly->payments && $monthly->payments->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Date</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Montant</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Méthode</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Référence</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Note</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Enregistré par</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($monthly->payments as $payment)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $payment->paid_at?->format('d/m/Y') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-accent-green-500 text-right">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ ucfirst($payment->method ?? '-') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $payment->reference ?? '-' }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600 max-w-xs truncate">{{ $payment->note ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $payment->recorder->name ?? '-' }}</td>
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
                        <p class="mt-1 text-xs text-gray-500">Maximum : {{ number_format($monthly->remaining_amount, 0, ',', ' ') }} FCFA</p>
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
                            <option value="versement_especes">Versement especes sur compte</option>
                            <option value="depot_bancaire">Depot bancaire</option>
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
@endsection
