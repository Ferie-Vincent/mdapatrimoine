@extends('layouts.app')

@section('title', 'Échéances mensuelles')

@section('actions')
    <div class="flex items-center gap-1.5">
        <x-export-dropdown route="exports.monthlies" :query="request()->query()" />
        @can('create', App\Models\LeaseMonthly::class)
        <form method="POST" action="{{ route('monthlies.generate') }}" class="inline"
              x-data x-on:submit.prevent="if(confirm('Générer les échéances pour les baux actifs ?')) $el.submit()">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Générer échéances
            </button>
        </form>
        @endcan
    </div>
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('monthlies.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="month" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Mois</label>
                <input type="month" name="month" id="month" value="{{ request('month') }}"
                       class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
            </div>
            <div>
                <label for="status" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Statut</label>
                <select name="status" id="status" class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous</option>
                    <option value="paye" {{ request('status') === 'paye' ? 'selected' : '' }}>Payé</option>
                    <option value="partiel" {{ request('status') === 'partiel' ? 'selected' : '' }}>Partiel</option>
                    <option value="impaye" {{ request('status') === 'impaye' ? 'selected' : '' }}>Impayé</option>
                    <option value="en_retard" {{ request('status') === 'en_retard' ? 'selected' : '' }}>En retard</option>
                    <option value="a_venir" {{ request('status') === 'a_venir' ? 'selected' : '' }}>A venir</option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Bail / Locataire..."
                           class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-200 bg-gray-50/70 text-sm placeholder-gray-400 hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all">
                </div>
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
        @if($monthlies->count())
            <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Mois</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Locataire</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Bien</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Loyer dû</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Pénalités</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Total dû</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Payé</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Reste</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Statut</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($monthlies as $monthly)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900">{{ $monthly->month }}</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-md bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shrink-0">
                                            <span class="text-white font-semibold text-[10px]">{{ strtoupper(substr($monthly->lease->tenant->first_name ?? '', 0, 1) . substr($monthly->lease->tenant->last_name ?? '', 0, 1)) }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $monthly->lease->tenant->full_name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $monthly->lease->property->reference ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 text-right">{{ number_format($monthly->rent_due, 0, ',', ' ') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 text-right">{{ number_format($monthly->penalty_due, 0, ',', ' ') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">{{ number_format($monthly->total_due, 0, ',', ' ') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-accent-green-500 text-right">{{ number_format($monthly->paid_amount, 0, ',', ' ') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-accent-red-500 text-right">{{ number_format($monthly->remaining_amount, 0, ',', ' ') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <x-badge type="{{ $monthly->status }}" label="{{ ['paye' => 'Payé', 'partiel' => 'Partiel', 'impaye' => 'Impayé', 'en_retard' => 'En retard', 'a_venir' => 'A venir'][$monthly->status] ?? $monthly->status }}" />
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('monthlies.show', $monthly) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Voir</a>
                                    @if($monthly->status !== 'paye')
                                        @can('create', App\Models\Payment::class)
                                        <button @click="$dispatch('open-modal', 'pay-monthly-{{ $monthly->id }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-accent-green-500 rounded-lg hover:bg-accent-green-600 transition shadow-sm"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>Payer</button>
                                        @endcan
                                    @endif
                                    @if($monthly->status === 'paye')
                                        @can('create', App\Models\Document::class)
                                        <form method="POST" action="{{ route('documents.generate-quittance') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="lease_monthly_id" value="{{ $monthly->id }}">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Quittance</button>
                                        </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $monthlies->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state message="Aucune échéance trouvée. Générez les échéances pour les baux actifs." />
        @endif
    </div>

    {{-- Payment Modals --}}
    @foreach($monthlies as $monthly)
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

                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">DÉTAILS DU PAIEMENT</p>
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
                    </div>

                    <div class="border-t border-gray-100 pt-5 mt-5">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">JUSTIFICATIF</p>
                        <x-file-upload name="receipt" label="Justificatif" />
                    </div>
                </x-form-modal>
            @endcan
        @endif
    @endforeach

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
@endpush
@endsection
