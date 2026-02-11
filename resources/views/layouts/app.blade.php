<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MDA-Patrimoine') - {{ config('app.name', 'MDA-Patrimoine') }}</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1E3A8A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/assets/img/pwa-192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Simple-DataTables -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@9/dist/style.css">

    @stack('styles')

    <style>
        body { font-family: 'Nunito', sans-serif; }
        /* Custom scrollbar for sidebar */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 9999px; }
        .sidebar-nav::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }
        /* DataTable overrides */
        .datatable-wrapper .datatable-top,
        .datatable-wrapper .datatable-bottom { display: none; }
        .datatable-sorter { cursor: pointer; position: relative; padding-right: 18px !important; }
        .datatable-sorter::before,
        .datatable-sorter::after { border-color: #9ca3af transparent; opacity: 0.5; }
        .datatable-sorter.asc::before,
        .datatable-sorter.desc::after { border-color: #1E3A8A transparent; opacity: 1; }
        .datatable-table th { background: inherit !important; }
        .datatable-table { width: 100% !important; }

        /* Global print styles */
        @media print {
            body { background: white !important; }
            aside, nav, .sidebar, [class*="sidebar"], .print\\:hidden,
            #toast-container { display: none !important; }
            .min-h-screen { min-height: auto !important; }
            .flex-1.lg\\:ml-\\[280px\\], [class*="lg:ml-"] { margin-left: 0 !important; }
            main { padding: 0 !important; }
            .print-logo-header { display: flex !important; }
            /* Preserve colors */
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body class="antialiased bg-gray-100" x-data="{ sidebarOpen: true, sidebarMobile: false }">
    <div class="min-h-screen flex">

        {{-- ============================================================ --}}
        {{-- SIDEBAR --}}
        {{-- ============================================================ --}}

        {{-- Overlay mobile --}}
        <div x-show="sidebarMobile" x-transition.opacity @click="sidebarMobile = false"
             class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden" style="display:none;"></div>

        <aside :class="sidebarMobile ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 w-[280px] bg-sidebar flex flex-col z-50 transition-transform duration-300
                      lg:translate-x-0 lg:z-30 print:hidden">

            {{-- Logo --}}
            <div class="flex items-center gap-3 h-[72px] px-6 border-b border-white/10 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('assets/img/logo-2.jpg') }}" alt="MDA" class="w-8 h-8 rounded object-cover">
                    </div>
                    <div>
                        <span class="text-base font-bold text-white tracking-tight block">MDA-Patrimoine</span>
                        <span class="text-[10px] text-white/50">Gestion immobiliere</span>
                    </div>
                </a>
            </div>

            {{-- SCI Selector --}}
            <div class="px-5 py-4 border-b border-white/10 shrink-0" x-data="{ open: false }">
                <label class="block text-[11px] font-medium uppercase tracking-wider text-white/40 mb-1.5">SCI active</label>
                <button @click="open = !open"
                        class="w-full flex items-center justify-between bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white/80 hover:bg-white/10 transition">
                    <span class="font-medium truncate">{{ $activeSci->name ?? 'Toutes les SCIs' }}</span>
                    <svg class="w-4 h-4 ml-2 text-white/40 transition-transform shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="mt-1.5 bg-sidebar-light border border-white/10 rounded-lg shadow-lg overflow-hidden">
                    @isset($userScis)
                        @if(auth()->user()->isSuperAdmin())
                            <form method="POST" action="{{ route('switch-sci') }}">
                                @csrf
                                <input type="hidden" name="sci_id" value="">
                                <button type="submit"
                                        class="block w-full text-left px-3 py-2 text-sm text-white/70 hover:bg-white/10 hover:text-white transition
                                               {{ !isset($activeSci) || $activeSci === null ? 'bg-white/10 text-white font-semibold' : '' }}">
                                    Toutes les SCIs
                                </button>
                            </form>
                        @endif
                        @foreach($userScis as $sci)
                            <form method="POST" action="{{ route('switch-sci') }}">
                                @csrf
                                <input type="hidden" name="sci_id" value="{{ $sci->id }}">
                                <button type="submit"
                                        class="block w-full text-left px-3 py-2 text-sm text-white/70 hover:bg-white/10 hover:text-white transition
                                               {{ isset($activeSci) && $activeSci->id === $sci->id ? 'bg-white/10 text-white font-semibold' : '' }}">
                                    {{ $sci->name }}
                                </button>
                            </form>
                        @endforeach
                    @endisset
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto sidebar-nav py-5 px-4 space-y-1">

                {{-- Section: Tableau de bord --}}
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-white/40 mb-2">Tableau de bord</p>

                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10-2a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
                    </svg>
                    Dashboard
                </a>

                @if(auth()->user()->isSuperAdmin() || (isset($userScis) && $userScis->count() > 1))
                <a href="{{ route('analytics.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('analytics.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('analytics.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Analytique
                </a>
                @endif

                {{-- Section: Gestion locative --}}
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-white/40 mt-6 mb-2">Gestion locative</p>

                <a href="{{ route('excel.database') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('excel.database') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('excel.database') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                    Base de donnees
                </a>

                <a href="{{ route('monthly-management.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('monthly-management.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('monthly-management.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Gestion des loyers mensuels
                </a>

                <a href="{{ route('financial-current.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('financial-current.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('financial-current.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Point financier courant
                </a>

                <a href="{{ route('service-providers.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('service-providers.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('service-providers.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Annuaire prestataires
                </a>

                <a href="{{ route('staff.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('staff.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('staff.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Personnel & Paie
                </a>

                {{-- Section: Documents --}}
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-white/40 mt-6 mb-2">Documents</p>

                <a href="{{ route('leases.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('leases.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('leases.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Baux
                </a>

                <a href="{{ route('tenants.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('tenants.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('tenants.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Locataires
                </a>

                <a href="{{ route('properties.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('properties.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('properties.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    Biens immobiliers
                </a>

                <a href="{{ route('gallery.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('gallery.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('gallery.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Galerie photos
                </a>

                {{-- Section: Administration (super_admin only) --}}
                @can('viewAny', App\Models\User::class)
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-white/40 mt-6 mb-2">Administration</p>

                <a href="{{ route('scis.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('scis.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('scis.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    SCIs
                </a>

                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('users.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('users.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Utilisateurs
                </a>

                <a href="{{ route('settings.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('settings.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('settings.*') ? 'text-accent-orange-400' : 'text-white/40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Parametres
                </a>
                @endcan
            </nav>

            {{-- User / Logout --}}
            <div class="border-t border-white/10 px-5 py-4 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-accent-orange-400 flex items-center justify-center shrink-0">
                            <span class="text-sm font-semibold text-white">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-white/40 truncate">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg text-white/40 hover:text-red-400 hover:bg-white/10 transition" title="Deconnexion">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ============================================================ --}}
        {{-- MAIN CONTENT --}}
        {{-- ============================================================ --}}
        <div class="flex-1 lg:ml-[280px] min-h-screen flex flex-col">

            {{-- Top bar --}}
            <header class="sticky top-0 z-20 bg-white border-b border-gray-200 print:hidden"
                    x-data="{ mobileActions: false, mobileSearch: false }">
                <div class="flex items-center justify-between h-14 sm:h-[72px] px-3 sm:px-6">
                    {{-- Left: hamburger + page title --}}
                    <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                        <button @click="sidebarMobile = !sidebarMobile"
                                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 lg:hidden shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <h1 class="text-sm sm:text-lg font-semibold text-gray-900 truncate">@yield('title', 'Tableau de bord')</h1>
                            @hasSection('breadcrumbs')
                                <nav class="text-xs text-gray-400 mt-0.5 hidden sm:block">
                                    @yield('breadcrumbs')
                                </nav>
                            @endif
                        </div>
                    </div>

                    {{-- Right: actions + user --}}
                    <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                        {{-- Desktop actions --}}
                        <div class="hidden md:flex items-center gap-1.5">
                            @yield('actions')
                        </div>

                        {{-- Mobile actions toggle (only if page has actions) --}}
                        @hasSection('actions')
                            <button @click="mobileActions = !mobileActions"
                                    class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        @endif

                        {{-- Mobile search toggle --}}
                        <button @click="mobileSearch = !mobileSearch"
                                class="sm:hidden p-2 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>

                        {{-- Desktop search --}}
                        <div class="relative hidden sm:block" x-data="globalSearch()" @click.away="open = false" @keydown.escape.window="open = false">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" x-model="query" @input.debounce.300ms="search" @focus="if(results.length) open = true"
                                       placeholder="Rechercher..." autocomplete="off"
                                       class="w-48 lg:w-64 pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition placeholder-gray-400">
                            </div>
                            <div x-show="open && (results.length > 0 || (query.length >= 2 && !loading))" x-transition
                                 class="absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-gray-200 rounded-xl shadow-xl z-50 max-h-96 overflow-y-auto" style="display:none;">
                                <template x-if="results.length === 0 && query.length >= 2 && !loading">
                                    <div class="px-4 py-6 text-center text-sm text-gray-400">Aucun resultat</div>
                                </template>
                                <template x-for="(group, type) in grouped" :key="type">
                                    <div>
                                        <div class="px-4 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 bg-gray-50/80 sticky top-0" x-text="type"></div>
                                        <template x-for="item in group" :key="item.url">
                                            <a :href="item.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-brand-50 transition">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                                     :class="item.icon === 'user' ? 'bg-blue-50 text-blue-500' : (item.icon === 'building' ? 'bg-purple-50 text-purple-500' : (item.icon === 'document' ? 'bg-amber-50 text-amber-500' : 'bg-green-50 text-green-500'))">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <template x-if="item.icon === 'user'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></template>
                                                        <template x-if="item.icon === 'building'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></template>
                                                        <template x-if="item.icon === 'document'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></template>
                                                        <template x-if="item.icon === 'cash'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></template>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="item.label"></p>
                                                    <p class="text-xs text-gray-400 truncate" x-text="item.sub"></p>
                                                </div>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Notification bell --}}
                        <button class="relative p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </button>

                        {{-- User avatar header --}}
                        <div class="hidden sm:flex items-center gap-2 pl-3 border-l border-gray-200" x-data="{ profileOpen: false }">
                            <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 hover:opacity-80 transition">
                                @if(Auth::user()->avatar_path)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar_path) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center">
                                        <span class="text-xs font-semibold text-brand-600">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-700 hidden lg:inline">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="profileOpen" @click.away="profileOpen = false" x-transition
                                 class="absolute right-6 top-16 bg-white border border-gray-200 rounded-xl shadow-lg w-56 py-2 z-50" style="display:none;">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-400">{{ Auth::user()->email ?? '' }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Deconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile actions dropdown --}}
                @hasSection('actions')
                    <div x-show="mobileActions" x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                         @click.away="mobileActions = false"
                         class="md:hidden border-t border-gray-100 bg-gray-50/80 px-3 py-2.5" style="display:none;">
                        <div class="flex flex-wrap items-center gap-2">
                            @yield('actions')
                        </div>
                    </div>
                @endif

                {{-- Mobile search overlay --}}
                <div x-show="mobileSearch" x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                     @click.away="mobileSearch = false"
                     class="sm:hidden border-t border-gray-100 bg-gray-50/80 px-3 py-2.5" style="display:none;"
                     x-data="globalSearch()" @keydown.escape.window="mobileSearch = false; open = false">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="query" @input.debounce.300ms="search" @focus="if(results.length) open = true"
                               x-init="$nextTick(() => $el.focus())"
                               placeholder="Rechercher..." autocomplete="off"
                               class="w-full pl-9 pr-3 py-2.5 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition placeholder-gray-400">
                    </div>
                    <div x-show="open && (results.length > 0 || (query.length >= 2 && !loading))" x-transition
                         class="mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto" style="display:none;">
                        <template x-if="results.length === 0 && query.length >= 2 && !loading">
                            <div class="px-4 py-6 text-center text-sm text-gray-400">Aucun resultat</div>
                        </template>
                        <template x-for="(group, type) in grouped" :key="type">
                            <div>
                                <div class="px-4 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 bg-gray-50/80 sticky top-0" x-text="type"></div>
                                <template x-for="item in group" :key="item.url">
                                    <a :href="item.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-brand-50 transition">
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                             :class="item.icon === 'user' ? 'bg-blue-50 text-blue-500' : (item.icon === 'building' ? 'bg-purple-50 text-purple-500' : (item.icon === 'document' ? 'bg-amber-50 text-amber-500' : 'bg-green-50 text-green-500'))">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <template x-if="item.icon === 'user'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></template>
                                                <template x-if="item.icon === 'building'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></template>
                                                <template x-if="item.icon === 'document'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></template>
                                                <template x-if="item.icon === 'cash'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></template>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate" x-text="item.label"></p>
                                            <p class="text-xs text-gray-400 truncate" x-text="item.sub"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </header>

            {{-- Toast notifications (Flowbite-style) --}}
            <div class="fixed top-4 right-4 z-50 flex flex-col gap-3 pointer-events-none print:hidden" id="toast-container">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                         class="pointer-events-auto flex items-center w-full max-w-sm p-4 bg-white rounded-lg shadow-lg border border-gray-100" role="alert">
                        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ms-3 text-sm font-normal text-gray-800">{{ session('success') }}</div>
                        <button @click="show = false" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                         class="pointer-events-auto flex items-center w-full max-w-sm p-4 bg-white rounded-lg shadow-lg border border-gray-100" role="alert">
                        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div class="ms-3 text-sm font-normal text-gray-800">{{ session('error') }}</div>
                        <button @click="show = false" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                         class="pointer-events-auto flex items-center w-full max-w-sm p-4 bg-white rounded-lg shadow-lg border border-gray-100" role="alert">
                        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-orange-500 bg-orange-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <div class="ms-3 text-sm font-normal text-gray-800">{{ session('warning') }}</div>
                        <button @click="show = false" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('info'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                         class="pointer-events-auto flex items-center w-full max-w-sm p-4 bg-white rounded-lg shadow-lg border border-gray-100" role="alert">
                        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-blue-500 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ms-3 text-sm font-normal text-gray-800">{{ session('info') }}</div>
                        <button @click="show = false" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div x-data="{ show: true }" x-show="show"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                         class="pointer-events-auto flex items-start w-full max-w-sm p-4 bg-white rounded-lg shadow-lg border border-gray-100" role="alert">
                        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div class="ms-3 text-sm font-normal text-gray-800 flex-1">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button @click="show = false" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 transition shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Print logo header (hidden on screen, visible on print) --}}
            <div class="print-logo-header hidden items-center justify-center gap-4 px-6 py-4 border-b-2 border-gray-200">
                <img src="{{ asset('assets/img/logo.jpg') }}" alt="Logo" class="h-14 w-auto object-contain">
                <div class="text-center">
                    <h1 class="text-base font-bold text-gray-900 uppercase tracking-wide">MDA-Patrimoine</h1>
                    <p class="text-[10px] text-gray-500">Gestion immobilière et de patrimoine</p>
                </div>
            </div>

            {{-- Page content --}}
            <main class="flex-1 p-4 sm:p-6 pb-20 lg:pb-6">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    <nav class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 lg:hidden print:hidden">
        <div class="flex items-center justify-around h-16 px-2">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 px-3 py-1 {{ request()->routeIs('dashboard') ? 'text-brand-500' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10-2a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/></svg>
                <span class="text-[10px] font-medium">Dashboard</span>
            </a>
            <a href="{{ route('properties.index') }}" class="flex flex-col items-center gap-1 px-3 py-1 {{ request()->routeIs('properties.*') ? 'text-brand-500' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span class="text-[10px] font-medium">Biens</span>
            </a>
            <a href="{{ route('leases.index') }}" class="flex flex-col items-center gap-1 px-3 py-1 {{ request()->routeIs('leases.*') ? 'text-brand-500' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-[10px] font-medium">Baux</span>
            </a>
            <a href="{{ route('monthly-management.index') }}" class="flex flex-col items-center gap-1 px-3 py-1 {{ request()->routeIs('monthly-management.*') ? 'text-brand-500' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-[10px] font-medium">Loyers</span>
            </a>
            <button @click="sidebarMobile = !sidebarMobile" class="flex flex-col items-center gap-1 px-3 py-1 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <span class="text-[10px] font-medium">Menu</span>
            </button>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9/dist/umd/simple-datatables.js"></script>
    <script>
        function SCIDataTable(selector, options = {}) {
            const el = document.querySelector(selector);
            if (!el) return null;
            const tbody = el.querySelector('tbody');
            if (!tbody || !tbody.querySelector('tr td') || tbody.querySelector('tr td[colspan]')) return null;
            const headers = el.querySelectorAll('thead th');
            const lastIdx = headers.length - 1;
            const lastText = headers[lastIdx] ? headers[lastIdx].textContent.trim().toLowerCase() : '';
            const lastIsActions = !lastText || lastText === 'actions';
            const columns = Array.from(headers).map((_, i) => {
                if (i === lastIdx && lastIsActions) return { select: i, sortable: false };
                return { select: i };
            });
            return new simpleDatatables.DataTable(selector, Object.assign({
                paging: false,
                searchable: false,
                labels: {
                    placeholder: "Rechercher...",
                    noRows: "Aucun résultat",
                    info: "Affichage de {start} à {end} sur {rows} entrées",
                },
                columns: columns,
            }, options));
        }
    </script>
    <script>
        function globalSearch() {
            return {
                query: '',
                results: [],
                open: false,
                loading: false,
                get grouped() {
                    const groups = {};
                    this.results.forEach(r => {
                        if (!groups[r.type]) groups[r.type] = [];
                        groups[r.type].push(r);
                    });
                    return groups;
                },
                async search() {
                    if (this.query.length < 2) { this.results = []; this.open = false; return; }
                    this.loading = true;
                    try {
                        const res = await fetch(`{{ route('search') }}?q=${encodeURIComponent(this.query)}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        this.results = await res.json();
                        this.open = true;
                    } catch (e) {
                        this.results = [];
                    }
                    this.loading = false;
                }
            };
        }
    </script>
    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>
</body>
</html>
