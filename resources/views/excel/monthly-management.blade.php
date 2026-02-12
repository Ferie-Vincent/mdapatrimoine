@extends('layouts.app')

@section('title', 'GESTION DES LOYERS MENSUELS')

@section('actions')
    <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Imprimer
    </button>
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('monthly-management.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="filter_month" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Mois</label>
                @php
                    $moisFr = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
                @endphp
                <select name="filter_month" id="filter_month" class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (int)explode('-', $month)[1] == $m ? 'selected' : '' }}>{{ $m }} - {{ $moisFr[$m] }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="filter_year" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Année</label>
                <select name="filter_year" id="filter_year" class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                    @for($y = 2020; $y <= (int)date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ (int)explode('-', $month)[0] == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mt-6">
        @if($monthlies->count())
            <table id="dataTable" class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">NOM</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">PRENOM</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">N° APPARTEMENT</th>
                        <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">LOYER MENSUEL</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">DATE DE PAIEMENT</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">MODE DE PAIEMENT</th>
                        <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">ARRIERE DE LOYER</th>
                        @can('create', App\Models\Payment::class)
                            <th class="px-6 py-3.5 text-center text-sm font-medium text-gray-400 print:hidden">ACTION</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($monthlies as $monthly)
                        @php
                            $tenant = $monthly->lease?->tenant;
                            $lastPayment = $monthly->payments->first();
                            $hasArrears = (float)$monthly->remaining_amount > 0;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tenant?->last_name ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $tenant?->first_name ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-brand-600 font-medium">{{ $monthly->lease?->property?->numero_porte ?? $monthly->lease?->dossier_number ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-right text-gray-600">{{ number_format((float)$monthly->rent_due, 0, ',', ' ') }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $lastPayment?->paid_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $lastPayment?->method ? ucfirst(str_replace('_', ' ', $lastPayment->method)) : '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-right {{ $hasArrears ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                {{ number_format((float)$monthly->remaining_amount, 0, ',', ' ') }}
                            </td>
                            @can('create', App\Models\Payment::class)
                                <td class="px-6 py-5 whitespace-nowrap text-center print:hidden">
                                    @if($hasArrears)
                                        <button type="button"
                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', {detail: 'pay-monthly-{{ $monthly->id }}'}))"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Régler
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-lg">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Payé
                                        </span>
                                    @endif
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $monthlies->links() }}
            </div>
        @else
            <x-empty-state message="Aucune échéance pour ce mois." />
        @endif
    </div>

    {{-- Payment Modals --}}
    @can('create', App\Models\Payment::class)
        @foreach($monthlies as $monthly)
            @if((float)$monthly->remaining_amount > 0)
                @php
                    $mTenant = $monthly->lease?->tenant;
                    $mProperty = $monthly->lease?->property;
                @endphp
                <x-form-modal name="pay-monthly-{{ $monthly->id }}" title="Régler l'arriéré" :action="route('payments.store')" submitLabel="Enregistrer le paiement" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>' iconColor="text-green-500">
                    {{-- Monthly info summary --}}
                    <div class="bg-brand-50/60 rounded-xl border border-brand-100 p-4 mb-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <p class="text-xs text-brand-500">Locataire</p>
                                <p class="text-sm font-semibold text-brand-900">{{ $mTenant?->full_name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-brand-500">Bien</p>
                                <p class="text-sm font-semibold text-brand-900">{{ $mProperty?->reference ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-brand-500">Mois</p>
                                <p class="text-sm font-semibold text-brand-900">{{ \Carbon\Carbon::createFromFormat('Y-m', $monthly->month)->translatedFormat('F Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-brand-500">Reste a payer</p>
                                <p class="text-sm font-bold text-red-700">{{ number_format((float)$monthly->remaining_amount, 0, ',', ' ') }} F</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="lease_monthly_id" value="{{ $monthly->id }}">

                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">DÉTAILS DU PAIEMENT</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Montant (FCFA) <span class="text-red-500">*</span></label>
                            <x-money-input name="amount" :value="(int)$monthly->remaining_amount" :required="true" />
                            <p class="mt-1 text-xs text-gray-500">Maximum : {{ number_format((float)$monthly->remaining_amount, 0, ',', ' ') }} FCFA</p>
                            <template x-if="errors.amount"><p class="mt-1 text-sm text-red-600" x-text="errors.amount[0]"></p></template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de paiement <span class="text-red-500">*</span></label>
                            <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
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
                                <option value="autre">Autre</option>
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
            @endif
        @endforeach
    @endcan

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
@endpush
@endsection
