@extends('layouts.app')

@section('title', $user->name)

@section('content')
    @php
        $roleLabels = ['super_admin' => 'Super Admin', 'gestionnaire' => 'Gestionnaire', 'lecture_seule' => 'Lecture seule'];
    @endphp

    {{-- Hero Header --}}
    <div class="relative bg-gradient-to-br from-brand-700 via-brand-500 to-indigo-500 dark:from-brand-900 dark:via-brand-700 dark:to-indigo-800 rounded-2xl overflow-hidden mb-6 print:hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 44px 44px;"></div>
        <div class="relative flex items-center justify-between px-5 pt-5">
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
            <div class="flex items-center gap-2">
                @can('update', $user)
                    <button @click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Modifier
                    </button>
                @endcan
                @can('delete', $user)
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center p-2 bg-white/10 hover:bg-red-500/80 backdrop-blur-sm rounded-lg text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="relative text-center px-6 pb-8 pt-6">
            <div class="flex items-center justify-center gap-2">
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-sm text-white border border-white/20">
                    {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                </span>
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-sm text-white border border-white/20">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $user->is_active ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                </span>
            </div>
            @if($user->avatar_path)
                <div class="mt-4"><img src="{{ asset('storage/' . $user->avatar_path) }}" alt="Avatar" class="w-16 h-16 rounded-xl object-cover ring-2 ring-white/20 mx-auto"></div>
            @endif
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-3">{{ $user->name }}</h1>
            <p class="text-white/70 mt-2">{{ $user->email }}</p>
        </div>
    </div>

    <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden">
        <div class="px-6 py-5 border-b border-theme-subtle">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Informations</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Nom</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $user->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Email</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Cree le</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-5">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-3">SCIs affectees</p>
            @if($user->scis->count())
                <div class="flex flex-wrap gap-2">
                    @foreach($user->scis as $sci)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-50 dark:bg-brand-950/30 text-brand-700 border border-brand-100 dark:border-gray-600">
                            {{ $sci->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-on-surface-faint">Aucune SCI affectee</p>
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
