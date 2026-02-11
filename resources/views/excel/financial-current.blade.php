@extends('layouts.app')

@section('title', 'POINT FINANCIER COURANT')

@section('actions')
    <div class="flex items-center gap-1.5">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition print:hidden shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimer
        </button>
        @can('create', App\Models\Lease::class)
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-prestation')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Prestation
        </button>
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-purchase')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 rounded-lg text-xs font-semibold text-white hover:bg-amber-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Achat
        </button>
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-fixed-charge')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sidebar rounded-lg text-xs font-semibold text-white hover:bg-sidebar-light transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Charge fixe
        </button>
        @endcan
    </div>
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('financial-current.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="month" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Mois</label>
                @php
                    $moisFr = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
                @endphp
                <select name="month" id="month" class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $moisFr[$m] }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="year" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Année</label>
                <input type="number" name="year" id="year" value="{{ $year }}" min="2020" max="2030"
                       class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
            </div>
        </div>
    </x-filters>

    {{-- CAISSE MENSUELLE --}}
    @can('create', App\Models\Lease::class)
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </span>
                Caisse Mensuelle
            </h3>
            <span class="text-sm text-gray-400 font-medium">{{ $moisFr[$month] }} {{ $year }}</span>
        </div>
        <div class="p-6">
            {{-- Stat cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="relative overflow-hidden rounded-xl border border-gray-100 p-4 bg-gradient-to-br from-white to-gray-50/80">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-50 text-brand-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Budget</p>
                            <p class="text-lg font-bold text-gray-900 leading-tight">{{ number_format($budgetAmount, 0, ',', ' ') }} <span class="text-sm font-medium text-gray-400">F</span></p>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-xl border border-blue-100/80 p-4 bg-gradient-to-br from-blue-50/40 to-white">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium text-blue-400 uppercase tracking-wider">Prestations</p>
                            <p class="text-lg font-bold text-blue-700 leading-tight">{{ number_format($totalProvisions, 0, ',', ' ') }} <span class="text-sm font-medium text-blue-400">F</span></p>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-xl border border-amber-100/80 p-4 bg-gradient-to-br from-amber-50/40 to-white">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium text-amber-400 uppercase tracking-wider">Achats + Charges</p>
                            <p class="text-lg font-bold text-amber-700 leading-tight">{{ number_format($totalPurchases + $totalFixedCharges, 0, ',', ' ') }} <span class="text-sm font-medium text-amber-400">F</span></p>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-xl border {{ $soldeCaisse >= 0 ? 'border-emerald-100/80' : 'border-red-100/80' }} p-4 bg-gradient-to-br {{ $soldeCaisse >= 0 ? 'from-emerald-50/40 to-white' : 'from-red-50/40 to-white' }}">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl {{ $soldeCaisse >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium {{ $soldeCaisse >= 0 ? 'text-emerald-400' : 'text-red-400' }} uppercase tracking-wider">Solde</p>
                            <p class="text-lg font-bold {{ $soldeCaisse >= 0 ? 'text-emerald-700' : 'text-red-600' }} leading-tight">{{ number_format($soldeCaisse, 0, ',', ' ') }} <span class="text-sm font-medium {{ $soldeCaisse >= 0 ? 'text-emerald-400' : 'text-red-400' }}">F</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Budget form --}}
            <form method="POST" action="{{ route('financial-current.store-budget') }}" class="flex flex-col sm:flex-row sm:items-end gap-3 bg-gray-50/80 rounded-xl p-4 border border-gray-100">
                @csrf
                <input type="hidden" name="sci_id" value="{{ $activeSci->id ?? '' }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Montant du budget mensuel</label>
                    <x-money-input name="amount" :value="$budget->amount ?? ''" placeholder="0" />
                </div>
                <button type="submit" class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-semibold hover:bg-brand-700 transition shadow-sm shrink-0">Définir</button>
            </form>
        </div>
    </div>
    @endcan

    {{-- PRESTATIONS (full width) --}}
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085"/></svg>
                </span>
                Prestations de services
            </h3>
            <span class="text-sm font-bold text-blue-600 bg-blue-50 rounded-full px-3.5 py-1">{{ number_format($totalProvisions, 0, ',', ' ') }} F</span>
        </div>

        <div class="overflow-x-auto">
            <table id="dataTable-prestations" class="min-w-full divide-y divide-gray-100 text-sm">
                <thead>
                    <tr>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Type</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Agent</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Date</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-right text-xs sm:text-sm font-medium text-gray-400">Montant</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">État</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Paiement</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-center text-xs sm:text-sm font-medium text-gray-400">Justif.</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-right text-xs sm:text-sm font-medium text-gray-400"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($provisions as $p)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">{{ $p->service_type }}</span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-800">{{ $p->agent }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500">{{ $p->service_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-right font-semibold text-gray-800">{{ $p->amount ? number_format((float)$p->amount, 0, ',', ' ') : '-' }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm">
                                @if($p->status === 'Terminé')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $p->status }}
                                    </span>
                                @elseif($p->status === 'Ajourné')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>{{ $p->status }}
                                    </span>
                                @elseif($p->status)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>{{ $p->status }}
                                    </span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500">
                                @switch($p->payment_method)
                                    @case('especes') Espèces @break
                                    @case('virement') Virement @break
                                    @case('cheque') Chèque @break
                                    @case('mobile_money') Mobile Money @break
                                    @default <span class="text-gray-300">-</span>
                                @endswitch
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                                @if($p->receipt_path)
                                    <a href="{{ asset('storage/' . $p->receipt_path) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-brand-600 hover:bg-brand-50 transition" title="Voir le justificatif">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    </a>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-right whitespace-nowrap">
                                @can('create', App\Models\Lease::class)
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('financial-current.attestation', ['type' => 'provision', 'id' => $p->id]) }}" target="_blank"
                                       class="inline-flex items-center justify-center w-8 h-8 text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 transition" title="Attestation de réception">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('financial-current.destroy-provision', $p) }}" class="inline" onsubmit="return confirm('Supprimer cette prestation ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-3 sm:px-6 py-8 sm:py-12 text-center text-xs sm:text-sm text-gray-400">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63"/></svg>
                            Aucune prestation enregistrée
                        </td></tr>
                    @endforelse
                </tbody>
                @if($provisions->count())
                <tfoot class="border-t-2 border-gray-100">
                    <tr>
                        <td colspan="3" class="px-3 sm:px-6 py-3 sm:py-4 font-semibold text-gray-700 text-xs sm:text-sm">Total Prestations</td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-right font-bold text-gray-900 text-xs sm:text-sm">{{ number_format($totalProvisions, 0, ',', ' ') }} FCFA</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ACHATS DE MATERIEL (full width) --}}
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                </span>
                Achats de matériel
            </h3>
            <span class="text-sm font-bold text-amber-600 bg-amber-50 rounded-full px-3.5 py-1">{{ number_format($totalPurchases, 0, ',', ' ') }} F</span>
        </div>

        <div class="overflow-x-auto">
            <table id="dataTable-purchases" class="min-w-full divide-y divide-gray-100 text-sm">
                <thead>
                    <tr>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Matériels</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Fournisseur</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Date d'achat</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-right text-xs sm:text-sm font-medium text-gray-400">Montant</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Paiement</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-center text-xs sm:text-sm font-medium text-gray-400">Justif.</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-right text-xs sm:text-sm font-medium text-gray-400"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($purchases as $p)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-800">{{ $p->materials }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-600">{{ $p->supplier }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500">{{ $p->purchase_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-right font-semibold text-gray-800">{{ $p->amount ? number_format((float)$p->amount, 0, ',', ' ') : '-' }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500">
                                @switch($p->payment_method)
                                    @case('especes') Espèces @break
                                    @case('virement') Virement @break
                                    @case('cheque') Chèque @break
                                    @case('mobile_money') Mobile Money @break
                                    @default <span class="text-gray-300">-</span>
                                @endswitch
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                                @if($p->receipt_path)
                                    <a href="{{ asset('storage/' . $p->receipt_path) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-brand-600 hover:bg-brand-50 transition" title="Voir le justificatif">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    </a>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-right whitespace-nowrap">
                                @can('create', App\Models\Lease::class)
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('financial-current.attestation', ['type' => 'purchase', 'id' => $p->id]) }}" target="_blank"
                                       class="inline-flex items-center justify-center w-8 h-8 text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 transition" title="Attestation de réception">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('financial-current.destroy-purchase', $p) }}" class="inline" onsubmit="return confirm('Supprimer cet achat ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 sm:px-6 py-8 sm:py-12 text-center text-xs sm:text-sm text-gray-400">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                            Aucun achat enregistré
                        </td></tr>
                    @endforelse
                </tbody>
                @if($purchases->count())
                <tfoot class="border-t-2 border-gray-100">
                    <tr>
                        <td colspan="3" class="px-3 sm:px-6 py-3 sm:py-4 font-semibold text-gray-700 text-xs sm:text-sm">Total Achats</td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-right font-bold text-gray-900 text-xs sm:text-sm">{{ number_format($totalPurchases, 0, ',', ' ') }} FCFA</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- CHARGES FIXES --}}
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 overflow-hidden" x-data="{ activeCharge: 'all' }">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-gray-500 to-gray-700 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </span>
                Charges fixes
            </h3>
            {{-- Tab navigation --}}
            <div class="overflow-x-auto -mx-6 px-6 sm:mx-0 sm:px-0">
                <div class="inline-flex items-center bg-gray-100 rounded-xl p-1 text-xs font-medium whitespace-nowrap">
                    <button @click="activeCharge = 'all'" :class="activeCharge === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 rounded-lg transition">
                        Tout <span class="ml-1 text-gray-400">({{ number_format($totalFixedCharges, 0, ',', ' ') }} F)</span>
                    </button>
                    @foreach(['cie' => 'CIE', 'sodeci' => 'SODECI', 'honoraire' => 'Honoraires'] as $ct => $cl)
                        @php $sub = (float) $fixedCharges->where('charge_type', $ct)->sum('amount'); @endphp
                        <button @click="activeCharge = '{{ $ct }}'" :class="activeCharge === '{{ $ct }}' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 rounded-lg transition">
                            {{ $cl }} <span class="ml-1 text-gray-400">({{ number_format($sub, 0, ',', ' ') }})</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead>
                    <tr>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Type</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Libellé</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-right text-xs sm:text-sm font-medium text-gray-400">Montant</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Date</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-left text-xs sm:text-sm font-medium text-gray-400">Mode</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-center text-xs sm:text-sm font-medium text-gray-400">Justif.</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-3.5 text-right text-xs sm:text-sm font-medium text-gray-400"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($fixedCharges as $fc)
                        <tr class="hover:bg-gray-50/50 transition" x-show="activeCharge === 'all' || activeCharge === '{{ $fc->charge_type }}'" x-cloak>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm">
                                @switch($fc->charge_type)
                                    @case('cie')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-yellow-50 text-yellow-700">CIE</span>
                                    @break
                                    @case('sodeci')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-cyan-50 text-cyan-700">SODECI</span>
                                    @break
                                    @case('honoraire')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-purple-50 text-purple-700">Honoraires</span>
                                    @break
                                @endswitch
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-800">{{ $fc->label ?? $fc->charge_type_label ?? '-' }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-right font-semibold text-gray-800">{{ number_format((float)$fc->amount, 0, ',', ' ') }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500">{{ $fc->payment_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500">
                                @switch($fc->payment_method)
                                    @case('especes') Espèces @break
                                    @case('virement') Virement @break
                                    @case('cheque') Chèque @break
                                    @case('mobile_money') Mobile Money @break
                                    @default <span class="text-gray-300">-</span>
                                @endswitch
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                                @if($fc->receipt_path)
                                    <a href="{{ asset('storage/' . $fc->receipt_path) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-brand-600 hover:bg-brand-50 transition" title="Voir le justificatif">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    </a>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-right whitespace-nowrap">
                                @can('create', App\Models\Lease::class)
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('financial-current.attestation', ['type' => 'fixed-charge', 'id' => $fc->id]) }}" target="_blank"
                                       class="inline-flex items-center justify-center w-8 h-8 text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 transition" title="Attestation de réception">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('financial-current.destroy-fixed-charge', $fc) }}" class="inline" onsubmit="return confirm('Supprimer cette charge ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 sm:px-6 py-8 sm:py-12 text-center text-xs sm:text-sm text-gray-400">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Aucune charge fixe enregistrée
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RÉCAPITULATIF GLOBAL --}}
    <div class="mt-6 rounded-2xl overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #0F1D3D 0%, #152750 50%, #1C3160 100%);">
        <div class="p-5 sm:p-8">
            <h3 class="text-sm font-semibold uppercase tracking-wider mb-4 sm:mb-5" style="color: rgba(255,255,255,0.45);">Récapitulatif — {{ $moisFr[$month] }} {{ $year }}</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                <div class="rounded-xl p-4" style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        <p class="text-xs font-medium uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Prestations</p>
                    </div>
                    <p class="text-xl font-bold text-white">{{ number_format($totalProvisions, 0, ',', ' ') }} <span class="text-sm font-normal" style="color: rgba(255,255,255,0.35);">F</span></p>
                </div>
                <div class="rounded-xl p-4" style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <p class="text-xs font-medium uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Achats</p>
                    </div>
                    <p class="text-xl font-bold text-white">{{ number_format($totalPurchases, 0, ',', ' ') }} <span class="text-sm font-normal" style="color: rgba(255,255,255,0.35);">F</span></p>
                </div>
                <div class="rounded-xl p-4" style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full" style="background: rgba(255,255,255,0.5);"></span>
                        <p class="text-xs font-medium uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Charges fixes</p>
                    </div>
                    <p class="text-xl font-bold text-white">{{ number_format($totalFixedCharges, 0, ',', ' ') }} <span class="text-sm font-normal" style="color: rgba(255,255,255,0.35);">F</span></p>
                </div>
            </div>

            <div class="pt-4 sm:pt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2" style="border-top: 1px solid rgba(255,255,255,0.12);">
                <span class="text-sm sm:text-base font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.6);">Montant global</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-white">{{ number_format($totalGlobal, 0, ',', ' ') }} <span class="text-base sm:text-lg font-semibold" style="color: rgba(255,255,255,0.45);">FCFA</span></span>
            </div>
        </div>
    </div>

    {{-- MODAL : Ajouter une prestation --}}
    @can('create', App\Models\Lease::class)
    <x-modal name="add-prestation" title="Nouvelle prestation" maxWidth="2xl">
        <form method="POST" action="{{ route('financial-current.store-provision') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="sci_id" value="{{ $activeSci->id ?? '' }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de prestation <span class="text-red-500">*</span></label>
                    <select name="service_type" required
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                        <option value="">Sélectionner...</option>
                        @foreach(['ELECTRICITE','PLOMBERIE','MENUISERIE','SERRURIE','VITRIER','CARRELAGE','PEINTURE','FERRONIER'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agent <span class="text-red-500">*</span></label>
                    <input type="text" name="agent" required placeholder="Nom de l'agent"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="service_date"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA)</label>
                    <x-money-input name="amount" placeholder="0" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
                    <select name="status"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                        <option value="">Sélectionner...</option>
                        <option value="En cours">En cours</option>
                        <option value="Terminé">Terminé</option>
                        <option value="Ajourné">Ajourné</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement</label>
                    <select name="payment_method"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                        <option value="">Sélectionner...</option>
                        <option value="especes">Espèces</option>
                        <option value="virement">Virement</option>
                        <option value="cheque">Chèque</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div>
                    <x-file-upload name="receipt" label="Justificatif" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'add-prestation')"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Ajouter la prestation
                </button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL : Ajouter un achat de matériel --}}
    <x-modal name="add-purchase" title="Nouvel achat de matériel" maxWidth="2xl">
        <form method="POST" action="{{ route('financial-current.store-purchase') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="sci_id" value="{{ $activeSci->id ?? '' }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matériels <span class="text-red-500">*</span></label>
                    <input type="text" name="materials" required placeholder="Description du matériel"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur <span class="text-red-500">*</span></label>
                    <input type="text" name="supplier" required placeholder="Nom du fournisseur"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'achat</label>
                    <input type="date" name="purchase_date"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA)</label>
                    <x-money-input name="amount" placeholder="0" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement</label>
                    <select name="payment_method"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                        <option value="">Sélectionner...</option>
                        <option value="especes">Espèces</option>
                        <option value="virement">Virement</option>
                        <option value="cheque">Chèque</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <x-file-upload name="receipt" label="Justificatif" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'add-purchase')"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-amber-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Ajouter l'achat
                </button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL : Ajouter une charge fixe --}}
    <x-modal name="add-fixed-charge" title="Nouvelle charge fixe" maxWidth="lg">
        <form method="POST" action="{{ route('financial-current.store-fixed-charge') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="sci_id" value="{{ $activeSci->id ?? '' }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ chargeType: '' }">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de charge <span class="text-red-500">*</span></label>
                    <select name="charge_type" required x-model="chargeType"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                        <option value="">Sélectionner...</option>
                        <option value="cie">CIE (Électricité)</option>
                        <option value="sodeci">SODECI (Eau)</option>
                        <option value="honoraire">Honoraires</option>
                    </select>
                </div>
                <div class="sm:col-span-2" x-show="chargeType === 'honoraire'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom et prénoms</label>
                    <input type="text" name="label" placeholder="Nom de la personne"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA) <span class="text-red-500">*</span></label>
                    <x-money-input name="amount" placeholder="0" :required="true" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de paiement</label>
                    <input type="date" name="payment_date"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement</label>
                    <select name="payment_method"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2.5">
                        <option value="">Sélectionner...</option>
                        <option value="especes">Espèces</option>
                        <option value="virement">Virement</option>
                        <option value="cheque">Chèque</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div>
                    <x-file-upload name="receipt" label="Justificatif" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'add-fixed-charge')"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-gray-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Ajouter la charge
                </button>
            </div>
        </form>
    </x-modal>
    @endcan

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => {
    SCIDataTable('#dataTable-prestations');
    SCIDataTable('#dataTable-purchases');
});</script>
@endpush
@endsection
