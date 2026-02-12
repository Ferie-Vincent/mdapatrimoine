@extends('layouts.app')

@section('title', 'BASE DE DONNEES')

@section('actions')
    <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Imprimer
    </button>
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('excel.database') }}">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom, prénom, n° appartement, référence bien..."
                           class="block w-full pl-9 pr-3 py-2 rounded-xl border-gray-200 bg-gray-50/50 text-sm placeholder-gray-400 focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition">
                </div>
            </div>
        </div>
    </x-filters>

    {{-- Cards grid --}}
    @if($leases->count())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mt-6">
            @foreach($leases as $index => $lease)
                <div class="relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col h-[480px]"
                     x-data="{ open: false, section: 'base' }">

                    {{-- Dropdown menu --}}
                    <div class="absolute top-3.5 end-3.5 z-10" x-data="{ menu: false }">
                        <button @click="menu = !menu" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 focus:outline-none focus:ring-2 focus:ring-brand-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="3" d="M6 12h.01m6 0h.01m5.99 0h.01"/></svg>
                        </button>
                        <div x-show="menu" @click.away="menu = false" x-transition class="absolute right-0 mt-1 bg-white border border-gray-100 rounded-xl shadow-lg w-48 z-20">
                            <ul class="p-1.5 text-sm">
                                <li>
                                    <a href="{{ route('dossier-card.show', $lease) }}" class="flex items-center gap-2.5 w-full px-3 py-2 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
                                        <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                                        </span>
                                        ID Dossier
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('leases.show', $lease) }}" class="flex items-center gap-2.5 w-full px-3 py-2 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
                                        <span class="w-7 h-7 rounded-lg bg-yellow-50 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </span>
                                        Fiche Locataire
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('monthly-management.index', ['search' => $lease->dossier_number]) }}" class="flex items-center gap-2.5 w-full px-3 py-2 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
                                        <span class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </span>
                                        Gestion Loyers
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Card header: avatar + name + dossier --}}
                    <div class="flex flex-col items-center pt-6 pb-4 px-5 shrink-0">
                        <div class="w-14 h-14 mb-3 rounded-full bg-brand-50 flex items-center justify-center">
                            <span class="text-brand-600 font-bold text-lg uppercase">{{ mb_substr($lease->tenant->last_name ?? '?', 0, 1) }}{{ mb_substr($lease->tenant->first_name ?? '', 0, 1) }}</span>
                        </div>
                        <h5 class="text-base font-semibold text-gray-900 text-center">{{ $lease->tenant->last_name ?? '-' }} {{ $lease->tenant->first_name ?? '' }}</h5>
                        <a href="{{ route('dossier-card.show', $lease) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 transition">
                            Appt {{ $lease->property->numero_porte ?? $lease->dossier_number ?? '-' }}
                        </a>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="flex items-center gap-1 text-xs text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $lease->tenant->phone ?? '-' }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-green-50 text-green-600 ring-1 ring-inset ring-green-500/20">
                                {{ number_format((float)$lease->rent_amount, 0, ',', ' ') }} F/mois
                            </span>
                        </div>
                    </div>

                    {{-- Section tabs --}}
                    <div class="border-t border-gray-100 shrink-0">
                        <nav class="flex text-xs font-medium text-center text-gray-500 px-2">
                            <button @click="section = 'base'" :class="section === 'base' ? 'text-brand-600 border-brand-600' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300'" class="flex-1 py-2.5 border-b-2 transition">Infos</button>
                            <button @click="section = 'entree'" :class="section === 'entree' ? 'text-brand-600 border-brand-600' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300'" class="flex-1 py-2.5 border-b-2 transition">Entrée</button>
                            <button @click="section = 'garant'" :class="section === 'garant' ? 'text-brand-600 border-brand-600' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300'" class="flex-1 py-2.5 border-b-2 transition">Garant</button>
                            <button @click="section = 'sortie'" :class="section === 'sortie' ? 'text-brand-600 border-brand-600' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300'" class="flex-1 py-2.5 border-b-2 transition">Sortie</button>
                        </nav>
                    </div>

                    {{-- Scrollable tab content area --}}
                    <div class="flex-1 overflow-y-auto min-h-0">

                        {{-- INFORMATIONS DE BASE DU LOCATAIRE --}}
                        <div x-show="section === 'base'" class="px-5 py-4 text-sm">
                            <dl class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Type d'appartement</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->property->apartment_type_label ?? ucfirst($lease->property->type ?? '-') }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Etage</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->property->floor_label ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Adresse du bien</dt>
                                    <dd class="text-sm font-medium text-gray-900 truncate max-w-[140px]" title="{{ $lease->property->address ?? '' }}">{{ $lease->property->address ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Agence gérante</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->agency_name ?? $lease->sci->name ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">N° pièce d'identité</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->tenant->id_number ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Etat des lieux d'entrée</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->entry_inventory_date?->format('d/m/Y') ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Compteur CIE</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->property->cie_meter_number ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Compteur SODECI</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->property->sodeci_meter_number ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- ENTREE DU LOCATAIRE --}}
                        <div x-show="section === 'entree'" x-cloak class="px-5 py-4 text-sm">
                            <dl class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Caution 2 mois</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->caution_2_mois ? number_format((float)$lease->caution_2_mois, 0, ',', ' ') . ' F' : '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Loyers avancés 2 mois</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->loyers_avances_2_mois ? number_format((float)$lease->loyers_avances_2_mois, 0, ',', ' ') . ' F' : '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Frais agence</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->frais_agence ? number_format((float)$lease->frais_agence, 0, ',', ' ') . ' F' : '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between pt-2.5 border-t border-gray-100">
                                    <dt class="text-xs font-semibold text-gray-600">Loyer mensuel</dt>
                                    <dd class="text-sm font-bold text-brand-600">{{ number_format((float)$lease->rent_amount, 0, ',', ' ') }} F</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- INFORMATIONS PERSONNES TIERCES --}}
                        <div x-show="section === 'garant'" x-cloak class="px-5 py-4 text-sm">
                            <dl class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Noms prénoms</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->tenant->guarantor_name ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Adresse</dt>
                                    <dd class="text-sm font-medium text-gray-900 truncate max-w-[140px]" title="{{ $lease->tenant->guarantor_address ?? '' }}">{{ $lease->tenant->guarantor_address ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Numéro PI</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->tenant->guarantor_id_number ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Profession</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->tenant->guarantor_profession ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Téléphone</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->tenant->guarantor_phone ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- SORTIE DU LOCATAIRE --}}
                        <div x-show="section === 'sortie'" x-cloak class="px-5 py-4 text-sm">
                            <dl class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Date de depot du preavis</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->notice_deposit_date?->format('d/m/Y') ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Date d'etat des lieux de sortie</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->exit_inventory_date?->format('d/m/Y') ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Montants des charges dues</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->charges_due_amount ? number_format((float)$lease->charges_due_amount, 0, ',', ' ') . ' F' : '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Caution rendue</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $lease->deposit_returned_amount ? number_format((float)$lease->deposit_returned_amount, 0, ',', ' ') . ' F' : '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-400">Dettes ou creances</dt>
                                    <dd class="text-sm font-medium text-gray-900 truncate max-w-[140px]" title="{{ $lease->debts_or_credits_note ?? '' }}">{{ $lease->debts_or_credits_note ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Sticky card footer: quick actions --}}
                    <div class="shrink-0 px-5 pb-5 pt-3 border-t border-gray-100 mt-auto">
                        <div class="flex gap-2">
                            <a href="{{ route('dossier-card.show', $lease) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-brand-600 text-white text-xs font-medium rounded-lg hover:bg-brand-700 transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                                Dossier
                            </a>
                            <a href="{{ route('leases.show', $lease) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white text-gray-600 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Fiche
                            </a>
                            <a href="{{ route('monthly-management.index', ['search' => $lease->dossier_number]) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white text-gray-600 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Loyers
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $leases->links() }}
        </div>
    @else
        <x-empty-state message="Aucun locataire trouvé." />
    @endif
@endsection
