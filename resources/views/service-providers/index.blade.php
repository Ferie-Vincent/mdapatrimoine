@extends('layouts.app')

@section('title', 'Annuaire prestataires')

@section('actions')
    <div class="flex items-center gap-1.5">
        <x-export-dropdown route="exports.service-providers" :query="request()->query()" />
        @can('create', App\Models\ServiceProvider::class)
        <button @click="$dispatch('open-modal', 'create-provider')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau prestataire
        </button>
        @endcan
    </div>
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('service-providers.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="search" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom, téléphone, email..."
                           class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-200 bg-gray-50/70 text-sm placeholder-gray-400 hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all">
                </div>
            </div>
            <div>
                <label for="category" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Catégorie</label>
                <select name="category" id="category" class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Toutes</option>
                    @foreach(['artisan' => 'Artisan', 'manoeuvre' => 'Manoeuvre', 'plombier' => 'Plombier', 'electricien' => 'Électricien', 'peintre' => 'Peintre', 'menuisier' => 'Menuisier', 'macon' => 'Maçon', 'serrurier' => 'Serrurier', 'climatiseur' => 'Climatiseur', 'autre' => 'Autre'] as $val => $label)
                        <option value="{{ $val }}" {{ request('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="is_active" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Statut</label>
                <select name="is_active" id="is_active" class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
        @if($providers->count())
            <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Nom</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Categorie</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Specialite</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Telephone</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Email</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Statut</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($providers as $provider)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-accent-orange-400 to-accent-orange-500 flex items-center justify-center shrink-0 shadow-sm">
                                            <span class="text-white font-semibold text-sm">{{ strtoupper(substr($provider->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $provider->name }}</div>
                                            @if($provider->company)
                                                <div class="text-xs text-gray-400">{{ $provider->company }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @php
                                        $catColor = match($provider->category) {
                                            'plombier'    => 'info',
                                            'electricien' => 'warning',
                                            'peintre'     => 'success',
                                            'menuisier'   => 'actif',
                                            'macon'       => 'danger',
                                            'serrurier'   => 'default',
                                            'climatiseur' => 'en_attente',
                                            default       => 'default',
                                        };
                                    @endphp
                                    <x-badge :type="$catColor">{{ $provider->category_label }}</x-badge>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $provider->specialty ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $provider->phone ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $provider->email ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($provider->is_active)
                                        <x-badge type="success">Actif</x-badge>
                                    @else
                                        <x-badge type="warning">Inactif</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="inline-flex items-center gap-2">
                                        @can('update', $provider)
                                            <button type="button" data-open-modal="edit-provider-{{ $provider->id }}"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Modifier</button>
                                        @endcan
                                        @can('delete', $provider)
                                            <form method="POST" action="{{ route('service-providers.destroy', $provider) }}" class="inline" onsubmit="return confirm('Supprimer ce prestataire ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Supprimer</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $providers->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state title="Aucun prestataire trouve" description="Commencez par ajouter un prestataire dans l'annuaire." />
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- CONTRACTS SECTION --}}
    {{-- ============================================================ --}}

    {{-- Expiring contracts alert --}}
    @if(isset($expiringContracts) && $expiringContracts->count())
        <div class="mt-6 rounded-xl bg-amber-50 border border-amber-200 p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">{{ $expiringContracts->count() }} contrat(s) expirant dans les 30 prochains jours</p>
                <ul class="mt-1 text-sm text-amber-700">
                    @foreach($expiringContracts as $ec)
                        <li>{{ $ec->title }} — {{ $ec->serviceProvider->name }} ({{ $ec->end_date->format('d/m/Y') }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-between mt-8 mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Contrats fournisseurs</h2>
        @can('create', App\Models\ServiceProvider::class)
            <button @click="$dispatch('open-modal', 'create-contract')"
                    class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouveau contrat
            </button>
        @endcan
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if(isset($contracts) && $contracts->count())
            <div class="overflow-x-auto">
                <table id="contractsTable" class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Titre</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Prestataire</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Montant</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Debut</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Fin</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Statut</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($contracts as $contract)
                            @php
                                $isExpiring = $contract->status === 'actif' && $contract->end_date && $contract->end_date->lte(now()->addDays(30)) && $contract->end_date->gte(now());
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition {{ $isExpiring ? 'bg-amber-50/30' : '' }}">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $contract->title }}
                                        @if($isExpiring)
                                            <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-accent-orange-100 text-accent-orange-500">Expire bientot</span>
                                        @endif
                                    </div>
                                    @if($contract->description)
                                        <div class="text-xs text-gray-400 truncate max-w-xs">{{ Str::limit($contract->description, 60) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $contract->serviceProvider->name }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ number_format((float) $contract->amount, 0, ',', ' ') }} F</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $contract->start_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $contract->end_date ? $contract->end_date->format('d/m/Y') : '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @php
                                        $statusType = match($contract->status) {
                                            'actif'   => 'success',
                                            'termine' => 'default',
                                            'annule'  => 'danger',
                                            default   => 'default',
                                        };
                                    @endphp
                                    <x-badge :type="$statusType">{{ $contract->status_label }}</x-badge>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="inline-flex items-center gap-2">
                                        @can('create', App\Models\ServiceProvider::class)
                                            <button type="button" data-open-modal="edit-contract-{{ $contract->id }}"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Modifier</button>
                                            <form method="POST" action="{{ route('provider-contracts.destroy', $contract) }}" class="inline" onsubmit="return confirm('Supprimer ce contrat ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Supprimer</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-empty-state title="Aucun contrat" description="Ajoutez un contrat fournisseur pour suivre les echeances et montants." />
        @endif
    </div>

    {{-- Create Provider Modal --}}
    @can('create', App\Models\ServiceProvider::class)
        <x-form-modal name="create-provider" title="Nouveau prestataire" :action="route('service-providers.store')" maxWidth="2xl" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.3-5.3a2.121 2.121 0 010-3l.708-.707a2.121 2.121 0 013 0l5.3 5.3m-7.07 7.07l7.07-7.07m0 0l5.3 5.3a2.121 2.121 0 010 3l-.707.707a2.121 2.121 0 01-3 0l-5.3-5.3"/></svg>' iconColor="text-orange-500">
            @include('service-providers._form', ['provider' => null])
        </x-form-modal>
    @endcan

    {{-- Edit Provider Modals --}}
    @foreach($providers as $provider)
        @can('update', $provider)
            <x-form-modal name="edit-provider-{{ $provider->id }}" title="Modifier {{ $provider->name }}" :action="route('service-providers.update', $provider)" method="PUT" maxWidth="2xl" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.3-5.3a2.121 2.121 0 010-3l.708-.707a2.121 2.121 0 013 0l5.3 5.3m-7.07 7.07l7.07-7.07m0 0l5.3 5.3a2.121 2.121 0 010 3l-.707.707a2.121 2.121 0 01-3 0l-5.3-5.3"/></svg>' iconColor="text-orange-500">
                @include('service-providers._form', ['provider' => $provider])
            </x-form-modal>
        @endcan
    @endforeach

    {{-- Create Contract Modal --}}
    @can('create', App\Models\ServiceProvider::class)
        <x-form-modal name="create-contract" title="Nouveau contrat fournisseur" :action="route('provider-contracts.store')" maxWidth="2xl" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.251 2.251 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>' iconColor="text-teal-500">
            @include('service-providers._contract-form', ['contract' => null])
        </x-form-modal>
    @endcan

    {{-- Edit Contract Modals --}}
    @if(isset($contracts))
        @foreach($contracts as $contract)
            @can('create', App\Models\ServiceProvider::class)
                <x-form-modal name="edit-contract-{{ $contract->id }}" title="Modifier le contrat" :action="route('provider-contracts.update', $contract)" method="PUT" maxWidth="2xl" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.251 2.251 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>' iconColor="text-teal-500">
                    @include('service-providers._contract-form', ['contract' => $contract])
                </x-form-modal>
            @endcan
        @endforeach
    @endif

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => { SCIDataTable('#dataTable'); SCIDataTable('#contractsTable'); });</script>
@endpush
@endsection
