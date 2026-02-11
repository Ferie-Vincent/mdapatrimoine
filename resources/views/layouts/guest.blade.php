<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MDA-Patrimoine') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Outfit', sans-serif; }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.6s ease-out both;
            }

            .animate-fade-in-up-delay {
                animation: fadeInUp 0.6s ease-out 0.15s both;
            }

            .animate-fade-in-up-delay-2 {
                animation: fadeInUp 0.6s ease-out 0.3s both;
            }
        </style>
    </head>
    <body class="antialiased text-gray-900">
        <div class="min-h-screen flex">
            {{-- Left side — form --}}
            <div class="w-full lg:w-1/2 flex flex-col justify-between px-6 sm:px-12 lg:px-16 xl:px-24 py-8 bg-gray-100">
                {{-- Logo --}}
                <div class="animate-fade-in-up">
                    <a href="/" class="inline-flex items-center gap-3">
                        <img src="{{ asset('assets/img/logo.jpg') }}" alt="MDA-Patrimoine" width="60%">
                        {{-- <div>
                            <span class="text-lg font-bold text-gray-900 tracking-tight leading-tight block">MDA-Patrimoine</span>
                            <span class="text-xs text-gray-500">Gestion immobiliere</span>
                        </div> --}}
                    </a>
                </div>

                {{-- Form area --}}
                <div class="w-full max-w-md mx-auto lg:mx-0 animate-fade-in-up-delay" style="margin-top: -20% !important;">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <div class="text-xs text-gray-400 animate-fade-in-up-delay-2">
                    &copy; {{ date('Y') }} MDA-Patrimoine. Tous droits reserves.
                </div>
            </div>

            {{-- Right side — image panel --}}
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
                {{-- Background with overlay --}}
                <div class="absolute inset-0 bg-brand-900">
                    <img
                        src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=80"
                        alt=""
                        class="w-full h-full object-cover opacity-40 mix-blend-luminosity"
                    >
                </div>

                {{-- Content over image --}}
                <div class="relative z-10 flex flex-col justify-between p-12 xl:p-16 text-white">
                    {{-- Top badge --}}
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 backdrop-blur-sm px-4 py-1.5 text-xs font-medium text-white/90 border border-white/10">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Plateforme de gestion v1.0
                        </span>
                    </div>

                    {{-- Main text --}}
                    <div>
                        <h1 class="text-3xl xl:text-4xl font-bold leading-tight">
                            La gestion patrimoniale<br>
                            <span class="text-brand-200 italic">simplifiee et efficace.</span>
                        </h1>
                        <p class="mt-4 text-sm text-white/70 max-w-md leading-relaxed">
                            Gerez vos SCI, biens immobiliers, baux et locataires depuis une interface centralisee et intuitive.
                        </p>

                        {{-- Stats --}}
                        <div class="mt-8 flex gap-8">
                            <div>
                                <div class="text-2xl font-bold">100%</div>
                                <div class="text-xs text-white/50 uppercase tracking-wider mt-1">Dematerialise</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">24/7</div>
                                <div class="text-xs text-white/50 uppercase tracking-wider mt-1">Accessible</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">Securise</div>
                                <div class="text-xs text-white/50 uppercase tracking-wider mt-1">Vos donnees</div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom --}}
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-brand-400 border-2 border-brand-900 flex items-center justify-center text-xs font-medium">M</div>
                            <div class="w-8 h-8 rounded-full bg-brand-300 border-2 border-brand-900 flex items-center justify-center text-xs font-medium text-brand-900">D</div>
                            <div class="w-8 h-8 rounded-full bg-brand-200 border-2 border-brand-900 flex items-center justify-center text-xs font-medium text-brand-900">A</div>
                        </div>
                        <span class="text-xs text-white/60">Utilise par les equipes MDA</span>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
