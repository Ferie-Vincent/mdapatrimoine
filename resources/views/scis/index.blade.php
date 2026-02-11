@extends('layouts.app')

@section('title', 'Societes (SCI)')

@section('actions')
    <div class="flex items-center gap-1.5">
        <x-export-dropdown route="exports.scis" :query="request()->query()" />
        @can('create', App\Models\Sci::class)
            <button @click="$dispatch('open-modal', 'create-sci')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle SCI
            </button>
        @endcan
    </div>
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('scis.index') }}">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Rechercher une SCI..."
                           class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-200 bg-gray-50/70 text-sm placeholder-gray-400 hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all">
                </div>
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
        @if($scis->count())
            <table id="dataTable" class="min-w-full">
                <thead class="">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Nom</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">RCCM</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">IFU</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Email</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Telephone</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Biens</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Statut</th>
                        <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($scis as $sci)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($sci->logo_path)
                                        <img class="h-9 w-9 rounded-lg object-cover mr-3" src="{{ Storage::url($sci->logo_path) }}" alt="{{ $sci->name }}">
                                    @else
                                        <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-accent-orange-400 to-accent-orange-500 flex items-center justify-center mr-3 shadow-sm">
                                            <span class="text-white font-semibold text-sm">{{ strtoupper(substr($sci->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <div class="text-sm font-medium text-gray-900">{{ $sci->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $sci->rccm ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $sci->ifu ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $sci->email ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $sci->phone ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium ring-1 ring-inset bg-brand-50 text-brand-600 ring-brand-500/20">
                                    {{ $sci->properties_count ?? $sci->properties->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($sci->is_active) <x-badge type="success">Actif</x-badge> @else <x-badge type="danger">Inactif</x-badge> @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('scis.show', $sci) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Voir</a>
                                @can('update', $sci)
                                    <button @click="$dispatch('open-modal', 'edit-sci-{{ $sci->id }}')"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Modifier</button>
                                @endcan
                                @can('delete', $sci)
                                    <form method="POST" action="{{ route('scis.destroy', $sci) }}" class="inline" onsubmit="return confirm('Supprimer cette SCI ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Supprimer</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">{{ $scis->links() }}</div>
        @else
            <x-empty-state title="Aucune SCI trouvee" description="Commencez par creer votre premiere SCI." />
        @endif
    </div>

    {{-- Create SCI Modal --}}
    @can('create', App\Models\Sci::class)
        <x-form-modal name="create-sci" title="Nouvelle SCI" :action="route('scis.store')" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M3.75 3v18m16.5-18v18M5.25 6h.008v.008H5.25V6zm0 3h.008v.008H5.25V9zm0 3h.008v.008H5.25V12zm6-6h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12zm6-6h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12z"/></svg>' iconColor="text-amber-500">
            @include('scis._form', ['sci' => null])
        </x-form-modal>
    @endcan

    {{-- Edit SCI Modals --}}
    @foreach($scis as $sci)
        @can('update', $sci)
            <x-form-modal name="edit-sci-{{ $sci->id }}" title="Modifier {{ $sci->name }}" :action="route('scis.update', $sci)" method="PUT" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M3.75 3v18m16.5-18v18M5.25 6h.008v.008H5.25V6zm0 3h.008v.008H5.25V9zm0 3h.008v.008H5.25V12zm6-6h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12zm6-6h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12z"/></svg>' iconColor="text-amber-500">
                @include('scis._form', ['sci' => $sci])
            </x-form-modal>
        @endcan
    @endforeach

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
@endpush
@endsection
