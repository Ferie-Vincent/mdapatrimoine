@extends('layouts.app')

@section('title', $user->name)

@section('actions')
    <div class="flex items-center gap-1.5">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimer
        </button>
        @can('update', $user)
            <button @click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 rounded-lg text-xs font-semibold text-white hover:bg-amber-700 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </button>
        @endcan
        @can('delete', $user)
            <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-accent-red-400 rounded-lg text-xs font-semibold text-white hover:bg-accent-red-500 transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Supprimer
                </button>
            </form>
        @endcan
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-slate-800 to-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($user->avatar_path)
                    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="Avatar" class="w-10 h-10 rounded-xl object-cover">
                @else
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                        <span class="text-sm font-bold text-white">{{ mb_substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
                <div>
                    <h3 class="text-lg font-semibold text-white">{{ $user->name }}</h3>
                    <p class="text-sm text-slate-300">{{ $user->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $roleColors = ['super_admin' => 'bg-red-400/20 text-red-300 ring-red-400/30', 'gestionnaire' => 'bg-blue-400/20 text-blue-300 ring-blue-400/30', 'lecture_seule' => 'bg-gray-400/20 text-gray-300 ring-gray-400/30'];
                    $roleLabels = ['super_admin' => 'Super Admin', 'gestionnaire' => 'Gestionnaire', 'lecture_seule' => 'Lecture seule'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ring-1 {{ $roleColors[$user->role] ?? '' }}">
                    {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ring-1 {{ $user->is_active ? 'bg-emerald-400/20 text-emerald-300 ring-emerald-400/30' : 'bg-amber-400/20 text-amber-300 ring-amber-400/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $user->is_active ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                </span>
            </div>
        </div>

        <div class="px-6 py-5 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Informations</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Nom</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Email</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Cree le</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">SCIs affectees</p>
            @if($user->scis->count())
                <div class="flex flex-wrap gap-2">
                    @foreach($user->scis as $sci)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-100">
                            {{ $sci->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">Aucune SCI affectee</p>
            @endif
        </div>
    </div>

    {{-- Edit User Modal --}}
    @can('update', $user)
        <x-form-modal name="edit-user-{{ $user->id }}" title="Modifier {{ $user->name }}" :action="route('users.update', $user)" method="PUT" maxWidth="2xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' iconColor="text-indigo-500">
            @include('users._form', ['user' => $user])
        </x-form-modal>
    @endcan
@endsection
