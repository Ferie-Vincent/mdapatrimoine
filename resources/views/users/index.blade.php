@extends('layouts.app')

@section('title', 'Utilisateurs')

@section('actions')
    @can('create', App\Models\User::class)
    <button @click="$dispatch('open-modal', 'create-user')"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvel utilisateur
    </button>
    @endcan
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('users.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom, e-mail..."
                           class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-200 bg-gray-50/70 text-sm placeholder-gray-400 hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all">
                </div>
            </div>
            <div>
                <label for="role" class="block text-xs font-semibold text-gray-500/80 uppercase tracking-wider mb-1">Rôle</label>
                <select name="role" id="role" class="block w-full rounded-xl border-gray-200 bg-gray-50/70 text-sm hover:border-brand-200 focus:bg-white focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="gestionnaire" {{ request('role') === 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
                    <option value="lecture_seule" {{ request('role') === 'lecture_seule' ? 'selected' : '' }}>Lecture seule</option>
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
        @if($users->count())
            <table id="dataTable" class="min-w-full">
                <thead class="">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Nom</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Role</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">SCIs</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Actif</th>
                        <th class="px-6 py-3.5 text-right text-sm font-medium text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shrink-0 shadow-sm">
                                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($user->role === 'super_admin') <x-badge type="danger">Super Admin</x-badge>
                                @elseif($user->role === 'gestionnaire') <x-badge type="info">Gestionnaire</x-badge>
                                @else <x-badge type="default">Lecture seule</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-600 max-w-xs truncate">{{ $user->scis->pluck('name')->join(', ') ?: '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($user->is_active) <x-badge type="success">Actif</x-badge> @else <x-badge type="warning">Inactif</x-badge> @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('users.show', $user) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Voir</a>
                                @can('update', $user)
                                    <button @click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Modifier</button>
                                @endcan
                                @can('delete', $user)
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Supprimer</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
        @else
            <x-empty-state title="Aucun utilisateur trouve" description="Commencez par ajouter un utilisateur." />
        @endif
    </div>

    {{-- Create User Modal --}}
    @can('create', App\Models\User::class)
        <x-form-modal name="create-user" title="Nouvel utilisateur" :action="route('users.store')" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' iconColor="text-indigo-500">
            @include('users._form', ['user' => null])
        </x-form-modal>
    @endcan

    {{-- Edit User Modals --}}
    @foreach($users as $user)
        @can('update', $user)
            <x-form-modal name="edit-user-{{ $user->id }}" title="Modifier {{ $user->name }}" :action="route('users.update', $user)" method="PUT" maxWidth="2xl" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' iconColor="text-indigo-500">
                @include('users._form', ['user' => $user])
            </x-form-modal>
        @endcan
    @endforeach

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
@endpush
@endsection
