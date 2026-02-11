@extends('layouts.app')

@section('title', 'Locataires')

@section('actions')
    <div class="flex items-center gap-1.5">
        <x-export-dropdown route="exports.tenants" :query="request()->query()" />
        @can('create', App\Models\Tenant::class)
        <button @click="$dispatch('open-modal', 'create-tenant')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau locataire
        </button>
        @endcan
    </div>
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('tenants.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom, téléphone, email..."
                           class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-200 bg-gray-50/70 text-sm placeholder-gray-400 hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all">
                </div>
            </div>
            <div>
                <label for="status" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Statut</label>
                <select name="status" id="status" class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
        @if($tenants->count())
            <table id="dataTable" class="min-w-full divide-y divide-gray-100">
                <thead class="">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Nom complet</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Telephone</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Email</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">SCI</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Profession</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Statut</th>
                        <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($tenants as $tenant)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center mr-3 shadow-sm">
                                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($tenant->first_name, 0, 1) . substr($tenant->last_name, 0, 1)) }}</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $tenant->full_name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $tenant->phone }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $tenant->email ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $tenant->sci->name ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $tenant->profession ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($tenant->is_active) <x-badge type="success">Actif</x-badge> @else <x-badge type="danger">Inactif</x-badge> @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('tenants.show', $tenant) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Voir</a>
                                    @can('update', $tenant)
                                        <button @click="$dispatch('open-modal', 'edit-tenant-{{ $tenant->id }}')"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Modifier</button>
                                    @endcan
                                    @can('delete', $tenant)
                                        <form method="POST" action="{{ route('tenants.destroy', $tenant) }}" class="inline" onsubmit="return confirm('Supprimer ce locataire ?')">
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
            <div class="px-6 py-4 border-t border-gray-100">{{ $tenants->links() }}</div>
        @else
            <x-empty-state title="Aucun locataire trouve" description="Commencez par ajouter un locataire." />
        @endif
    </div>

    {{-- Create Tenant Wizard Modal --}}
    @can('create', App\Models\Tenant::class)
        <x-wizard-modal name="create-tenant" title="Nouveau locataire" :action="route('tenants.store')" :hasFiles="true"
            :steps="['Identite', 'Piece d\'identite', 'Professionnel', 'Contact urgence', 'Garant']" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>' iconColor="text-blue-500">

            {{-- Step 1: Identite --}}
            <div x-show="currentStep === 0" data-step="0">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">SCI</label>
                        @if($activeSci ?? null)
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $activeSci->name }}</p>
                            <input type="hidden" name="sci_id" value="{{ $activeSci->id }}">
                        @else
                            <select name="sci_id" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <option value="">-- Selectionner --</option>
                                @foreach(\App\Models\Sci::all() as $sci)
                                    <option value="{{ $sci->id }}">{{ $sci->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <template x-if="errors.sci_id"><p class="mt-1 text-sm text-red-600" x-text="errors.sci_id[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prenom</label>
                        <input type="text" name="first_name" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <template x-if="errors.first_name"><p class="mt-1 text-sm text-red-600" x-text="errors.first_name[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <template x-if="errors.last_name"><p class="mt-1 text-sm text-red-600" x-text="errors.last_name[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <template x-if="errors.email"><p class="mt-1 text-sm text-red-600" x-text="errors.email[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telephone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <template x-if="errors.phone"><p class="mt-1 text-sm text-red-600" x-text="errors.phone[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telephone secondaire</label>
                        <input type="text" name="phone_secondary" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Adresse</label>
                        <textarea name="address" rows="2" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden"></textarea>
                    </div>
                </div>
            </div>

            {{-- Step 2: Piece d'identite --}}
            <div x-show="currentStep === 1" data-step="1">
                <div class="space-y-6">
                    {{-- Informations --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type de piece</label>
                            <select name="id_type" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                                <option value="">-- Selectionner --</option>
                                <option value="CNI">CNI</option>
                                <option value="Passeport">Passeport</option>
                                <option value="Permis">Permis de conduire</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Numero</label>
                            <input type="text" name="id_number" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                            <input type="date" name="id_expiration" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                    </div>
                    {{-- Documents --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Documents</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-file-upload name="id_file" label="Recto de la piece" accept="image/*,.pdf" hint="Image ou PDF (max 5 Mo)" />
                            </div>
                            <div>
                                <x-file-upload name="id_file_verso" label="Verso de la piece" accept="image/*,.pdf" hint="Image ou PDF (max 5 Mo)" />
                                <template x-if="errors.id_file_verso"><p class="mt-1 text-sm text-red-600" x-text="errors.id_file_verso[0]"></p></template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Professionnel --}}
            <div x-show="currentStep === 2" data-step="2">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Profession</label>
                            <input type="text" name="profession" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employeur</label>
                            <input type="text" name="employer" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Justificatif</p>
                        <x-file-upload name="payment_receipt" label="Justificatif de paiement" accept="image/*,.pdf" hint="Photo ou PDF (max 5 Mo)" />
                        <template x-if="errors.payment_receipt"><p class="mt-1 text-sm text-red-600" x-text="errors.payment_receipt[0]"></p></template>
                    </div>
                </div>
            </div>

            {{-- Step 4: Contact d'urgence --}}
            <div x-show="currentStep === 3" data-step="3">
                <p class="text-sm text-gray-500 mb-5">Personne a contacter en cas d'urgence.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom du contact</label>
                        <input type="text" name="emergency_contact_name" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telephone du contact</label>
                        <input type="text" name="emergency_contact_phone" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    </div>
                </div>
            </div>

            {{-- Step 5: Garant --}}
            <div x-show="currentStep === 4" data-step="4">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom du garant</label>
                            <input type="text" name="guarantor_name" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telephone du garant</label>
                            <input type="text" name="guarantor_phone" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">N° piece du garant</label>
                            <input type="text" name="guarantor_id_number" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Profession du garant</label>
                            <input type="text" name="guarantor_profession" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Adresse du garant</label>
                        <textarea name="guarantor_address" rows="2" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden"></textarea>
                    </div>
                </div>
            </div>
        </x-wizard-modal>
    @endcan

    {{-- Edit Tenant Wizard Modals --}}
    @foreach($tenants as $tenant)
        @can('update', $tenant)
            <x-wizard-modal name="edit-tenant-{{ $tenant->id }}" title="Modifier {{ $tenant->full_name }}" :action="route('tenants.update', $tenant)" method="PUT" :hasFiles="true"
                :steps="['Identite', 'Piece d\'identite', 'Professionnel', 'Contact urgence', 'Garant']" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>' iconColor="text-blue-500">
                @include('tenants._wizard_steps', ['t' => $tenant])
            </x-wizard-modal>
        @endcan
    @endforeach

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
@endpush
@endsection
