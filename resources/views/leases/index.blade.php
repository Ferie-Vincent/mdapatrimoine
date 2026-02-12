@extends('layouts.app')

@section('title', 'Baux')

@section('actions')
    <div class="flex items-center gap-1.5">
        <x-export-dropdown route="exports.leases" :query="request()->query()" />
        @can('create', App\Models\Lease::class)
        <button @click="$dispatch('open-modal', 'create-lease')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau bail
        </button>
        @endcan
    </div>
@endsection

@section('content')
    {{-- Tabs: Baux actifs / Archives --}}
    @php $isArchive = request('status') === 'resilie'; @endphp
    <div class="flex items-center gap-1 mb-6 border-b border-gray-200">
        <a href="{{ route('leases.index', array_merge(request()->except('status', 'page'), [])) }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 transition {{ !$isArchive ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Baux en cours
        </a>
        <a href="{{ route('leases.index', array_merge(request()->except('page'), ['status' => 'resilie'])) }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 transition {{ $isArchive ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            Archives
        </a>
    </div>

    {{-- Filters --}}
    <x-filters action="{{ route('leases.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @if(!$isArchive)
            <div>
                <label for="status" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Statut</label>
                <select name="status" id="status" class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous</option>
                    <option value="actif" {{ request('status') === 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                </select>
            </div>
            @else
                <input type="hidden" name="status" value="resilie">
            @endif
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Locataire ou bien..."
                           class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-200 bg-gray-50/70 text-sm placeholder-gray-400 hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all">
                </div>
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
        @if($leases->count())
            <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full divide-y divide-gray-100">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Bien</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Locataire</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Loyer</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Frais agence</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Debut</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Fin</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Statut</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($leases as $lease)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $lease->property->reference ?? '-' }}</div>
                                            <div class="text-xs text-gray-400">{{ $lease->property->address ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900">{{ $lease->tenant->full_name ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">{{ number_format($lease->rent_amount, 0, ',', ' ') }} F</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 text-right">{{ number_format($lease->charges_amount, 0, ',', ' ') }} F</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $lease->start_date?->format('d/m/Y') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $lease->end_date?->format('d/m/Y') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <x-badge :type="$lease->status" :label="match($lease->status) { 'actif' => 'Actif', 'resilie' => 'Resilie', 'en_attente' => 'En attente', 'expire' => 'Expire', default => $lease->status }" />
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('leases.show', $lease) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Voir</a>
                                        @if(!in_array($lease->status, ['resilie', 'termine']))
                                            @can('update', $lease)
                                                <button @click="$dispatch('open-modal', 'edit-lease-{{ $lease->id }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Modifier</button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">{{ $leases->withQueryString()->links() }}</div>
        @else
            <x-empty-state message="Aucun bail trouve." />
        @endif
    </div>

    {{-- Create Lease Wizard Modal --}}
    @can('create', App\Models\Lease::class)
        <x-wizard-modal name="create-lease" title="Nouveau bail" :action="route('leases.store')" :hasFiles="true"
            :steps="['Parties', 'Termes du bail', 'Conditions', 'Documents']" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>' iconColor="text-purple-500">

            {{-- Step 1: Parties --}}
            <div x-show="currentStep === 0" data-step="0">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SCI</label>
                        @if($activeSci ?? null)
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $activeSci->name }}</p>
                            <input type="hidden" name="sci_id" value="{{ $activeSci->id }}">
                        @else
                            <select name="sci_id" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden" onchange="window._leaseFilterSelects(this.value)">
                                <option value="">Selectionner</option>
                                @foreach(($properties ?? collect())->pluck('sci')->filter()->unique('id') as $sci)
                                    <option value="{{ $sci->id }}">{{ $sci->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <template x-if="errors.sci_id"><p class="mt-1 text-sm text-red-600" x-text="errors.sci_id[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bien <span class="text-red-500">*</span></label>
                        <select name="property_id" id="create-lease-property" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            <option value="">Selectionner</option>
                            @foreach($properties ?? [] as $prop)
                                <option value="{{ $prop->id }}" data-sci="{{ $prop->sci_id }}">{{ $prop->reference }} - {{ $prop->address }}</option>
                            @endforeach
                        </select>
                        <template x-if="errors.property_id"><p class="mt-1 text-sm text-red-600" x-text="errors.property_id[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Locataire <span class="text-red-500">*</span></label>
                        <select name="tenant_id" id="create-lease-tenant" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            <option value="">Selectionner</option>
                            @foreach($tenants ?? [] as $t)
                                <option value="{{ $t->id }}" data-sci="{{ $t->sci_id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
                            @endforeach
                        </select>
                        <template x-if="errors.tenant_id"><p class="mt-1 text-sm text-red-600" x-text="errors.tenant_id[0]"></p></template>
                    </div>
                </div>
            </div>

            {{-- Step 2: Termes du bail --}}
            <div x-show="currentStep === 1" data-step="1" x-data="leaseDates()" x-effect="computeDuration()">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de debut <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" x-model="startDate" @change="onStartChange()" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <template x-if="errors.start_date"><p class="mt-1 text-sm text-red-600" x-text="errors.start_date[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de fin <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" x-model="endDate" :min="startDate" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <template x-if="errors.end_date"><p class="mt-1 text-sm text-red-600" x-text="errors.end_date[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Duree (mois)</label>
                        <input type="number" name="duration_months" :value="duration" min="1" readonly class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-800 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Loyer (FCFA) <span class="text-red-500">*</span></label>
                        <x-money-input name="rent_amount" :required="true" />
                        <template x-if="errors.rent_amount"><p class="mt-1 text-sm text-red-600" x-text="errors.rent_amount[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Frais d'agence (FCFA)</label>
                        <x-money-input name="charges_amount" value="0" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Caution (FCFA)</label>
                        <x-money-input name="deposit_amount" value="0" />
                    </div>
                </div>

                {{-- Auto-calculated entry amounts --}}
                <div class="mt-5 pt-4 border-t border-dashed border-gray-200"
                     x-data="{ rentRaw: 0 }"
                     x-init="$el.closest('[data-step]').addEventListener('input', () => $nextTick(() => { const el = $el.closest('[data-step]').querySelector('input[name=rent_amount]'); rentRaw = el ? (parseInt(el.value) || 0) : 0; }))">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Montants d'entree (calcul automatique)</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Caution (2 mois)</label>
                            <div class="mt-1 h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-gray-600 flex items-center justify-between">
                                <span x-text="(rentRaw * 2).toLocaleString('fr-FR')">0</span>
                                <span class="text-xs font-medium text-gray-400">FCFA</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Avances loyers (2 mois)</label>
                            <div class="mt-1 h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-gray-600 flex items-center justify-between">
                                <span x-text="(rentRaw * 2).toLocaleString('fr-FR')">0</span>
                                <span class="text-xs font-medium text-gray-400">FCFA</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Frais d'agence (1 mois)</label>
                            <div class="mt-1 h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-gray-600 flex items-center justify-between">
                                <span x-text="rentRaw.toLocaleString('fr-FR')">0</span>
                                <span class="text-xs font-medium text-gray-400">FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Conditions --}}
            <div x-show="currentStep === 2" data-step="2">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                        <select name="payment_method" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            <option value="">Selectionner</option>
                            <option value="especes">Especes</option>
                            <option value="virement">Virement</option>
                            <option value="cheque">Cheque</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="versement_especes">Versement especes sur compte</option>
                            <option value="depot_bancaire">Depot bancaire</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jour d'echeance</label>
                        <select name="due_day" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            @for($i = 1; $i <= 28; $i++)
                                <option value="{{ $i }}" {{ $i === 1 ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut <span class="text-red-500">*</span></label>
                        <select name="status" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            <option value="actif" selected>Actif</option>
                            <option value="en_attente">En attente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Taux de penalite (%)</label>
                        <input type="number" name="penalty_rate" value="0" min="0" max="100" step="0.01" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Delai avant penalite (jours)</label>
                        <input type="number" name="penalty_delay_days" value="0" min="0" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    </div>
                </div>
            </div>

            {{-- Step 4: Documents & Notes --}}
            <div x-show="currentStep === 3" data-step="3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-file-upload name="signed_lease" label="Bail signe (PDF)" accept=".pdf,.jpg,.jpeg,.png" hint="PDF, JPG ou PNG (max 10 Mo)" />
                    <x-file-upload name="entry_inspection" label="Etat des lieux d'entree" accept=".pdf,.jpg,.jpeg,.png" hint="PDF, JPG ou PNG (max 10 Mo)" />
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="4" placeholder="Notes supplementaires..." class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden"></textarea>
                    </div>
                </div>
            </div>
        </x-wizard-modal>
    @endcan

    {{-- Edit Lease Wizard Modals --}}
    @foreach($leases as $lease)
        @can('update', $lease)
            <x-wizard-modal name="edit-lease-{{ $lease->id }}" title="Modifier le bail #{{ $lease->id }}" :action="route('leases.update', $lease)" method="PUT" :hasFiles="true"
                :steps="['Parties', 'Termes du bail', 'Conditions', 'Documents', 'Dossier Excel', 'Notes']" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>' iconColor="text-purple-500">

                {{-- Step 1: Parties --}}
                <div x-show="currentStep === 0" data-step="0">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SCI</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $lease->sci->name ?? '-' }}</p>
                            <input type="hidden" name="sci_id" value="{{ $lease->sci_id }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bien <span class="text-red-500">*</span></label>
                            <select name="property_id" required {{ $lease->status === 'actif' ? 'disabled' : '' }} class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">Selectionner</option>
                                @foreach($properties ?? [] as $prop)
                                    <option value="{{ $prop->id }}" data-sci="{{ $prop->sci_id }}" {{ $prop->id == $lease->property_id ? 'selected' : '' }}>{{ $prop->reference }} - {{ $prop->address }}</option>
                                @endforeach
                            </select>
                            @if($lease->status === 'actif')
                                <input type="hidden" name="property_id" value="{{ $lease->property_id }}">
                            @endif
                            <template x-if="errors.property_id"><p class="mt-1 text-sm text-red-600" x-text="errors.property_id[0]"></p></template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Locataire <span class="text-red-500">*</span></label>
                            <select name="tenant_id" required {{ $lease->status === 'actif' ? 'disabled' : '' }} class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">Selectionner</option>
                                @foreach($tenants ?? [] as $t)
                                    <option value="{{ $t->id }}" data-sci="{{ $t->sci_id }}" {{ $t->id == $lease->tenant_id ? 'selected' : '' }}>{{ $t->first_name }} {{ $t->last_name }}</option>
                                @endforeach
                            </select>
                            @if($lease->status === 'actif')
                                <input type="hidden" name="tenant_id" value="{{ $lease->tenant_id }}">
                            @endif
                            <template x-if="errors.tenant_id"><p class="mt-1 text-sm text-red-600" x-text="errors.tenant_id[0]"></p></template>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Termes du bail --}}
                <div x-show="currentStep === 1" data-step="1" x-data="leaseDates('{{ $lease->start_date?->format('Y-m-d') }}', '{{ $lease->end_date?->format('Y-m-d') }}')" x-effect="computeDuration()">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de debut <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" x-model="startDate" @change="onStartChange()" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            <template x-if="errors.start_date"><p class="mt-1 text-sm text-red-600" x-text="errors.start_date[0]"></p></template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de fin <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" x-model="endDate" :min="startDate" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            <template x-if="errors.end_date"><p class="mt-1 text-sm text-red-600" x-text="errors.end_date[0]"></p></template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Duree (mois)</label>
                            <input type="number" name="duration_months" :value="duration" min="1" readonly class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-800 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Loyer (FCFA) <span class="text-red-500">*</span></label>
                            <x-money-input name="rent_amount" :value="$lease->rent_amount" :required="true" />
                            <template x-if="errors.rent_amount"><p class="mt-1 text-sm text-red-600" x-text="errors.rent_amount[0]"></p></template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Frais d'agence (FCFA)</label>
                            <x-money-input name="charges_amount" :value="$lease->charges_amount" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Caution (FCFA)</label>
                            <x-money-input name="deposit_amount" :value="$lease->deposit_amount" />
                        </div>
                    </div>
                </div>

                {{-- Step 3: Conditions --}}
                <div x-show="currentStep === 2" data-step="2">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                            <select name="payment_method" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <option value="">Selectionner</option>
                                <option value="especes" {{ $lease->payment_method === 'especes' ? 'selected' : '' }}>Especes</option>
                                <option value="virement" {{ $lease->payment_method === 'virement' ? 'selected' : '' }}>Virement</option>
                                <option value="cheque" {{ $lease->payment_method === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="mobile_money" {{ $lease->payment_method === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="versement_especes" {{ $lease->payment_method === 'versement_especes' ? 'selected' : '' }}>Versement especes sur compte</option>
                                <option value="depot_bancaire" {{ $lease->payment_method === 'depot_bancaire' ? 'selected' : '' }}>Depot bancaire</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jour d'echeance</label>
                            <select name="due_day" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                @for($i = 1; $i <= 28; $i++)
                                    <option value="{{ $i }}" {{ ($lease->due_day ?? 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Statut <span class="text-red-500">*</span></label>
                            <select name="status" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <option value="actif" {{ $lease->status === 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="en_attente" {{ $lease->status === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Taux de penalite (%)</label>
                            <input type="number" name="penalty_rate" value="{{ $lease->penalty_rate }}" min="0" max="100" step="0.01" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Delai avant penalite (jours)</label>
                            <input type="number" name="penalty_delay_days" value="{{ $lease->penalty_delay_days }}" min="0" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                    </div>
                </div>

                {{-- Step 4: Documents --}}
                <div x-show="currentStep === 3" data-step="3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-file-upload name="signed_lease" label="Bail signe (PDF)" accept=".pdf,.jpg,.jpeg,.png" hint="PDF, JPG ou PNG (max 10 Mo)" :current="$lease->signed_lease_path ? basename($lease->signed_lease_path) : null" />
                        <x-file-upload name="entry_inspection" label="Etat des lieux d'entree" accept=".pdf,.jpg,.jpeg,.png" hint="PDF, JPG ou PNG (max 10 Mo)" :current="$lease->entry_inspection_path ? basename($lease->entry_inspection_path) : null" />
                    </div>
                </div>

                {{-- Step 5: Dossier Excel --}}
                <div x-show="currentStep === 4" data-step="4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">N° Appartement</label>
                            <input type="text" name="dossier_number" value="{{ $lease->dossier_number }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agence gerante</label>
                            <input type="text" name="agency_name" value="{{ $lease->agency_name }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date etat des lieux d'entree</label>
                            <input type="date" name="entry_inventory_date" value="{{ $lease->entry_inventory_date?->format('Y-m-d') }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Caution 2 mois (FCFA)</label>
                            <x-money-input name="caution_2_mois" :value="$lease->caution_2_mois" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Loyers avances 2 mois (FCFA)</label>
                            <x-money-input name="loyers_avances_2_mois" :value="$lease->loyers_avances_2_mois" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Frais agence (FCFA)</label>
                            <x-money-input name="frais_agence" :value="$lease->frais_agence" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date depot preavis</label>
                            <input type="date" name="notice_deposit_date" value="{{ $lease->notice_deposit_date?->format('Y-m-d') }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date etat lieux sortie</label>
                            <input type="date" name="exit_inventory_date" value="{{ $lease->exit_inventory_date?->format('Y-m-d') }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de sortie effective</label>
                            <input type="date" name="actual_exit_date" value="{{ $lease->actual_exit_date?->format('Y-m-d') }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                    </div>
                </div>

                {{-- Step 6: Notes --}}
                <div x-show="currentStep === 5" data-step="5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="4" placeholder="Notes supplementaires..." class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ $lease->notes }}</textarea>
                    </div>
                </div>
            </x-wizard-modal>
        @endcan
    @endforeach

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
<script>
function leaseDates(initStart, initEnd) {
    return {
        startDate: initStart || '',
        endDate: initEnd || '',
        duration: '',
        computeDuration() {
            if (!this.startDate || !this.endDate) { this.duration = ''; return; }
            const s = new Date(this.startDate);
            const e = new Date(this.endDate);
            if (e <= s) { this.duration = ''; return; }
            let months = (e.getFullYear() - s.getFullYear()) * 12 + (e.getMonth() - s.getMonth());
            if (e.getDate() < s.getDate()) months--;
            this.duration = Math.max(months, 1);
        },
        onStartChange() {
            if (this.endDate && this.endDate <= this.startDate) {
                this.endDate = '';
            }
        }
    };
}
</script>
@endpush
@endsection
