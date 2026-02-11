@extends('layouts.app')

@section('title', 'Galerie photos')

@section('content')

    {{-- Header --}}
    <div class="mb-6">
        <span class="text-brand-600 font-semibold tracking-wider text-xs uppercase mb-1 block">Médiathèque</span>
        <h1 class="text-2xl text-gray-800 font-medium tracking-tight">Albums Photos</h1>
        <p class="text-gray-500 mt-1 text-sm max-w-xl">
            Explorez les photos de vos biens immobiliers, classées par bien.
        </p>
    </div>

    {{-- Filters --}}
    <x-filters action="{{ route('gallery.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Recherche</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Référence, adresse..."
                           class="block w-full pl-9 pr-3 py-2 rounded-xl border-gray-200 bg-gray-50/50 text-sm placeholder-gray-400 focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition">
                </div>
            </div>
            <div>
                <label for="type" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Type de bien</label>
                <select name="type" id="type" class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                    <option value="">Tous</option>
                    <option value="appartement" {{ request('type') === 'appartement' ? 'selected' : '' }}>Appartement</option>
                    <option value="maison" {{ request('type') === 'maison' ? 'selected' : '' }}>Maison</option>
                    <option value="studio" {{ request('type') === 'studio' ? 'selected' : '' }}>Studio</option>
                    <option value="bureau" {{ request('type') === 'bureau' ? 'selected' : '' }}>Bureau</option>
                    <option value="commerce" {{ request('type') === 'commerce' ? 'selected' : '' }}>Commerce</option>
                    <option value="terrain" {{ request('type') === 'terrain' ? 'selected' : '' }}>Terrain</option>
                    <option value="autre" {{ request('type') === 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Statut</label>
                <select name="status" id="status" class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                    <option value="">Tous</option>
                    <option value="disponible" {{ request('status') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="occupe" {{ request('status') === 'occupe' ? 'selected' : '' }}>Occupé</option>
                    <option value="travaux" {{ request('status') === 'travaux' ? 'selected' : '' }}>En travaux</option>
                </select>
            </div>
        </div>
    </x-filters>

    {{-- Photo counter --}}
    <div class="mt-5 mb-4 flex items-center gap-2 text-sm text-gray-500">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span>{{ $totalPhotos }} photo{{ $totalPhotos > 1 ? 's' : '' }} — {{ $properties->total() }} bien{{ $properties->total() > 1 ? 's' : '' }}</span>
    </div>

    @if($properties->count())

        {{-- Albums grid (CEDEAO-style) --}}
        <div x-data="galleryApp()" class="min-h-[400px]">

            {{-- Albums overview --}}
            <div x-show="!openAlbum" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($properties as $pIndex => $property)
                    @php $photos = $property->photos; $count = count($photos); @endphp
                    <div class="group cursor-pointer" @click="openAlbumView({{ $pIndex }})">
                        {{-- Stacked card effect --}}
                        <div class="relative">
                            {{-- Back cards (stacked effect) --}}
                            @if($count >= 3)
                                <div class="absolute inset-0 rounded-xl border-4 border-white bg-white shadow-sm transform rotate-3 translate-x-1 translate-y-1"></div>
                            @endif
                            @if($count >= 2)
                                <div class="absolute inset-0 rounded-xl border-4 border-white bg-white shadow-sm transform -rotate-2 -translate-x-0.5 translate-y-0.5"></div>
                            @endif

                            {{-- Main card --}}
                            <div class="relative rounded-xl border-4 border-white bg-white shadow-md overflow-hidden transition-all duration-500 group-hover:scale-[1.02] group-hover:shadow-lg">
                                <img src="{{ asset('storage/' . $photos[0]) }}" alt="{{ $property->reference }}"
                                     class="w-full aspect-square object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">

                                {{-- Gradient overlay (visible on hover) --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                                {{-- Info overlay --}}
                                <div class="absolute bottom-0 left-0 right-0 p-3 flex justify-between items-end">
                                    <div class="min-w-0">
                                        <span class="text-[10px] group-hover:text-sm font-bold text-white uppercase tracking-tight block truncate drop-shadow transition-all duration-500">{{ $property->reference }}</span>
                                        <span class="text-[8px] group-hover:text-xs text-white/80 block truncate drop-shadow transition-all duration-500">{{ $property->address }}</span>
                                    </div>
                                    <span class="text-[8px] group-hover:text-[10px] bg-white/20 backdrop-blur-sm text-white px-2 py-0.5 rounded-full font-medium whitespace-nowrap ml-2 transition-all duration-500">{{ $count }} Photo{{ $count > 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Type badge --}}
                        @if($property->type)
                            <div class="mt-2 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-brand-50 text-brand-600 capitalize">{{ $property->type }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Expanded album view --}}
            <div x-show="openAlbum !== null" x-cloak
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

                {{-- Back button --}}
                <div class="mb-6">
                    <button @click="closeAlbumView()" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 text-sm font-medium shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour aux albums
                    </button>
                </div>

                {{-- Album header --}}
                <template x-if="openAlbum !== null && albums[openAlbum]">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
                        <div class="flex flex-wrap items-center gap-3 mb-1">
                            <a :href="albums[openAlbum].showUrl" class="text-lg font-semibold text-brand-600 hover:text-brand-800 transition" x-text="albums[openAlbum].reference"></a>
                            <template x-if="albums[openAlbum].type">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-50 text-brand-600 capitalize" x-text="albums[openAlbum].type"></span>
                            </template>
                            <span class="text-sm text-gray-500" x-text="albums[openAlbum].address"></span>
                            <span class="ml-auto text-xs text-gray-400" x-text="albums[openAlbum].photos.length + ' photo' + (albums[openAlbum].photos.length > 1 ? 's' : '')"></span>
                        </div>
                        @if($properties->first()?->sci)
                            <p class="text-xs text-gray-400 mt-1" x-show="albums[openAlbum]?.sci" x-text="'SCI : ' + (albums[openAlbum]?.sci || '')"></p>
                        @endif
                    </div>
                </template>

                {{-- Photos grid (polaroid style) --}}
                <template x-if="openAlbum !== null && albums[openAlbum]">
                    <div class="bg-gray-50/50 rounded-2xl p-6 min-h-[300px]">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            <template x-for="(photo, idx) in albums[openAlbum].photos" :key="idx">
                                <div class="group cursor-pointer" @click="openLightbox(idx)">
                                    <div class="relative rounded-lg border-4 border-white bg-white shadow-md overflow-hidden transition-all duration-300 group-hover:shadow-lg group-hover:scale-[1.03]">
                                        <img :src="photo" :alt="'Photo ' + (idx + 1)"
                                             class="w-full h-40 object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Lightbox (CEDEAO-style) --}}
            <div x-show="lightboxOpen" x-cloak
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[60] bg-black/95 backdrop-blur-sm flex flex-col items-center justify-center p-4"
                 @keydown.escape.window="closeLightbox()"
                 @keydown.left.window="prevPhoto()"
                 @keydown.right.window="nextPhoto()"
                 @click.self="closeLightbox()">

                {{-- Close button --}}
                <button @click="closeLightbox()" class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Navigation prev --}}
                <button x-show="currentAlbumPhotos().length > 1" @click="prevPhoto()"
                        class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors z-10 p-2">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Image --}}
                <div class="max-w-5xl max-h-[85vh] w-full relative flex flex-col items-center">
                    <img :src="currentPhoto()" alt="Full view"
                         class="w-auto max-w-full max-h-[80vh] object-contain rounded shadow-2xl" loading="lazy">
                    {{-- Caption --}}
                    <div class="mt-4 text-center">
                        <p class="text-white/80 text-sm font-light tracking-wide"
                           x-text="albums[openAlbum]?.reference + ' — Photo ' + (lightboxIndex + 1) + ' / ' + currentAlbumPhotos().length"></p>
                        <p class="text-white/50 text-xs mt-1" x-text="albums[openAlbum]?.address"></p>
                    </div>
                </div>

                {{-- Navigation next --}}
                <button x-show="currentAlbumPhotos().length > 1" @click="nextPhoto()"
                        class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors z-10 p-2">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $properties->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 p-16 mt-6 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-gray-50 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">Aucun bien avec des photos</p>
            <p class="text-xs text-gray-400 mt-1">Ajoutez des photos depuis la fiche d'un bien immobilier.</p>
        </div>
    @endif

    <script>
        function galleryApp() {
            return {
                openAlbum: null,
                lightboxOpen: false,
                lightboxIndex: 0,
                albums: @js($properties->getCollection()->values()->map(fn($p) => [
                    'reference' => $p->reference,
                    'address' => $p->address,
                    'type' => $p->type,
                    'sci' => $p->sci?->name,
                    'showUrl' => route('properties.show', $p),
                    'photos' => collect($p->photos)->map(fn($photo) => asset('storage/' . $photo))->values(),
                ])),

                openAlbumView(index) {
                    this.openAlbum = index;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                closeAlbumView() {
                    this.openAlbum = null;
                },
                currentAlbumPhotos() {
                    if (this.openAlbum === null || !this.albums[this.openAlbum]) return [];
                    return this.albums[this.openAlbum].photos;
                },
                currentPhoto() {
                    const photos = this.currentAlbumPhotos();
                    return photos[this.lightboxIndex] || '';
                },
                openLightbox(idx) {
                    this.lightboxIndex = idx;
                    this.lightboxOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                closeLightbox() {
                    this.lightboxOpen = false;
                    document.body.style.overflow = '';
                },
                prevPhoto() {
                    if (!this.lightboxOpen) return;
                    const photos = this.currentAlbumPhotos();
                    this.lightboxIndex = (this.lightboxIndex - 1 + photos.length) % photos.length;
                },
                nextPhoto() {
                    if (!this.lightboxOpen) return;
                    const photos = this.currentAlbumPhotos();
                    this.lightboxIndex = (this.lightboxIndex + 1) % photos.length;
                },
            };
        }
    </script>

@endsection
