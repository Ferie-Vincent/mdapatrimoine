@extends('layouts.app')

@section('title', 'PERSONNEL')

@section('actions')
    @can('create', App\Models\Lease::class)
    <div class="flex items-center gap-1.5">
        <x-export-dropdown route="exports.staff" :query="request()->query()" label="Personnel" />
        <x-export-dropdown route="exports.staff-payroll" :query="array_merge(request()->query(), ['month' => $month, 'year' => $year])" label="Paie" />
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-staff')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau personnel
        </button>
        @if($staff->count())
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-payroll')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 rounded-lg text-xs font-semibold text-white hover:bg-green-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            Enregistrer paie
        </button>
        @endif
    </div>
    @endcan
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('staff.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="month" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Mois (Paie)</label>
                @php
                    $moisFr = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
                @endphp
                <select name="month" id="month" class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $moisFr[$m] }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="year" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Année</label>
                <input type="number" name="year" id="year" value="{{ $year }}" min="2020" max="2030"
                       class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
            </div>
            <div>
                <label for="status" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Statut</label>
                <select name="status" id="status" class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>
        </div>
    </x-filters>

    {{-- ANNUAIRE DU PERSONNEL --}}
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-brand-50 text-brand-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                Annuaire du personnel
            </h3>
            <span class="text-xs font-medium text-gray-400 bg-gray-100 rounded-full px-3 py-1">{{ $staff->count() }} membre(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table id="dataTable-directory" class="min-w-full divide-y divide-gray-100 text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3.5 text-left text-sm font-medium text-gray-400">NOM & PRENOMS</th>
                        <th class="px-4 py-3.5 text-left text-sm font-medium text-gray-400">POSTE</th>
                        <th class="px-4 py-3.5 text-left text-sm font-medium text-gray-400">TELEPHONE</th>
                        <th class="px-4 py-3.5 text-left text-sm font-medium text-gray-400">EMAIL</th>
                        <th class="px-4 py-3.5 text-right text-sm font-medium text-gray-400">SALAIRE DE BASE</th>
                        <th class="px-4 py-3.5 text-left text-sm font-medium text-gray-400">EMBAUCHE</th>
                        <th class="px-4 py-3.5 text-right text-sm font-medium text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($staff as $member)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shrink-0 shadow-sm">
                                        <span class="text-white font-semibold text-xs">{{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}</span>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $member->last_name }} {{ $member->first_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-5 text-gray-600">{{ $member->role ?? '-' }}</td>
                            <td class="px-4 py-5 text-gray-600">{{ $member->phone ?? '-' }}</td>
                            <td class="px-4 py-5 text-gray-600">{{ $member->email ?? '-' }}</td>
                            <td class="px-4 py-5 text-right text-gray-600">{{ $member->base_salary ? number_format((float)$member->base_salary, 0, ',', ' ') . ' F' : '-' }}</td>
                            <td class="px-4 py-5 text-gray-600">{{ $member->hire_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-5 text-right">
                                @can('create', App\Models\Lease::class)
                                <form method="POST" action="{{ route('staff.destroy', $member) }}" class="inline" onsubmit="return confirm('Supprimer ce personnel ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Supprimer
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">Aucun personnel enregistré</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- GESTION DE PAIE --}}
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-accent-green-50 text-accent-green-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                </span>
                Paie — {{ $moisFr[$month] ?? $month }} {{ $year }}
            </h3>
            <span class="text-sm font-bold text-accent-green-500 bg-accent-green-50 rounded-full px-3 py-1">{{ number_format($totalPayroll, 0, ',', ' ') }} FCFA</span>
        </div>

        <div class="overflow-x-auto">
            <table id="dataTable-payroll" class="min-w-full divide-y divide-gray-100 text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3.5 text-left text-sm font-medium text-gray-400">PERSONNEL</th>
                        <th class="px-4 py-3.5 text-right text-sm font-medium text-gray-400">MONTANT</th>
                        <th class="px-4 py-3.5 text-left text-sm font-medium text-gray-400">DATE PAIEMENT</th>
                        <th class="px-4 py-3.5 text-left text-sm font-medium text-gray-400">MODE</th>
                        <th class="px-4 py-3.5 text-left text-sm font-medium text-gray-400">REFERENCE</th>
                        <th class="px-4 py-3.5 text-center text-sm font-medium text-gray-400">JUSTIF.</th>
                        <th class="px-4 py-3.5 text-right text-sm font-medium text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($staff as $member)
                        @php $p = $payrolls->get($member->id); @endphp
                        <tr class="hover:bg-gray-50/50 transition {{ $p ? '' : 'bg-accent-orange-50/30' }}">
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shrink-0 shadow-sm">
                                        <span class="text-white font-semibold text-xs">{{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}</span>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $member->last_name }} {{ $member->first_name }}</span>
                                </div>
                            </td>
                            @if($p)
                                <td class="px-4 py-5 text-right text-gray-600">{{ number_format((float)$p->amount, 0, ',', ' ') }} F</td>
                                <td class="px-4 py-5 text-gray-600">{{ $p->paid_at?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-5 text-gray-600">
                                    @switch($p->payment_method)
                                        @case('especes') Espèces @break
                                        @case('virement') Virement @break
                                        @case('cheque') Chèque @break
                                        @case('mobile_money') Mobile Money @break
                                        @default - @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-5 text-gray-600">{{ $p->reference ?? '-' }}</td>
                                <td class="px-4 py-5 text-center">
                                    @if($p->receipt_path)
                                        <a href="{{ asset('storage/' . $p->receipt_path) }}" target="_blank" class="text-brand-600 hover:text-brand-800">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-5 text-right">
                                    @can('create', App\Models\Lease::class)
                                    <form method="POST" action="{{ route('staff.destroy-payroll', $p) }}" class="inline" onsubmit="return confirm('Supprimer cette paie ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">Suppr.</button>
                                    </form>
                                    @endcan
                                </td>
                            @else
                                <td class="px-4 py-5 text-right text-accent-orange-400 italic">Non payé</td>
                                <td colspan="5" class="px-4 py-5 text-gray-400 text-center italic">--</td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">Aucun personnel</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-gray-100">
                    <tr>
                        <td class="px-4 py-3.5 font-semibold text-gray-900">TOTAL PAIE</td>
                        <td class="px-4 py-3.5 text-right font-bold text-gray-900">{{ number_format($totalPayroll, 0, ',', ' ') }} FCFA</td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- MODAL : Ajouter un membre du personnel --}}
    @can('create', App\Models\Lease::class)
    <x-modal name="add-staff" title="Nouveau membre du personnel" maxWidth="2xl">
        <form method="POST" action="{{ route('staff.store') }}">
            @csrf
            <input type="hidden" name="sci_id" value="{{ $activeSci->id ?? '' }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" required placeholder="Nom de famille"
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénoms <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" required placeholder="Prénoms"
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poste / Fonction</label>
                    <input type="text" name="role" placeholder="Ex: Gardien, Agent d'entretien..."
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="phone" placeholder="Numéro de téléphone"
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" placeholder="Adresse email"
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" name="address" placeholder="Adresse"
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salaire de base (FCFA)</label>
                    <x-money-input name="base_salary" placeholder="0" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                    <input type="date" name="hire_date"
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'add-staff')"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Ajouter
                </button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL : Enregistrer une paie --}}
    @if($staff->count())
    <x-modal name="add-payroll" title="Enregistrer une paie" maxWidth="2xl">
        <form method="POST" action="{{ route('staff.store-payroll') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="sci_id" value="{{ $activeSci->id ?? '' }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personnel <span class="text-red-500">*</span></label>
                    <select name="staff_member_id" required
                            class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <option value="">Sélectionner un membre...</option>
                        @foreach($staff as $member)
                            @if(!$payrolls->has($member->id))
                                <option value="{{ $member->id }}">{{ $member->last_name }} {{ $member->first_name }} {{ $member->base_salary ? '(' . number_format((float)$member->base_salary, 0, ',', ' ') . ' F)' : '' }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA) <span class="text-red-500">*</span></label>
                    <x-money-input name="amount" placeholder="Montant" :required="true" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de paiement <span class="text-red-500">*</span></label>
                    <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement <span class="text-red-500">*</span></label>
                    <select name="payment_method" required
                            class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <option value="">Sélectionner...</option>
                        <option value="especes">Espèces</option>
                        <option value="virement">Virement</option>
                        <option value="cheque">Chèque</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Référence</label>
                    <input type="text" name="reference" placeholder="N° chèque, réf. virement..."
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                    <input type="text" name="note" placeholder="Note optionnelle"
                           class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <x-file-upload name="receipt" label="Justificatif" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'add-payroll')"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer la paie
                </button>
            </div>
        </form>
    </x-modal>
    @endif
    @endcan

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => {
    SCIDataTable('#dataTable-directory');
    SCIDataTable('#dataTable-payroll');
});</script>
@endpush
@endsection
