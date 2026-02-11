@extends('layouts.app')

@section('title', 'Bien ' . $property->reference)

@section('actions')
    <div class="flex items-center gap-1.5">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimer
        </button>
        @can('update', $property)
            <button @click="$dispatch('open-modal', 'edit-property-{{ $property->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 rounded-lg text-xs font-semibold text-white hover:bg-amber-700 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </button>
        @endcan
        @can('delete', $property)
            <form method="POST" action="{{ route('properties.destroy', $property) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bien ?')">
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
    {{-- Detail Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 mb-6 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 bg-gradient-to-r from-slate-800 to-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">{{ $property->reference }}</h3>
                    <p class="text-sm text-slate-300">{{ ucfirst($property->type) }} &mdash; {{ $property->sci->name }}</p>
                </div>
            </div>
            @php
                $statusColors = [
                    'disponible' => 'bg-emerald-400/20 text-emerald-300 ring-emerald-400/30',
                    'occupe' => 'bg-blue-400/20 text-blue-300 ring-blue-400/30',
                    'travaux' => 'bg-amber-400/20 text-amber-300 ring-amber-400/30',
                ];
                $statusLabels = ['disponible' => 'Disponible', 'occupe' => 'Occupé', 'travaux' => 'En travaux'];
                $dotColors = ['disponible' => 'bg-emerald-400', 'occupe' => 'bg-blue-400', 'travaux' => 'bg-amber-400'];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ring-1 {{ $statusColors[$property->status] ?? 'bg-gray-400/20 text-gray-300 ring-gray-400/30' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $dotColors[$property->status] ?? 'bg-gray-400' }}"></span>
                {{ $statusLabels[$property->status] ?? ucfirst($property->status) }}
            </span>
        </div>

        {{-- Location --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-slate-50/50">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-brand-100 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4.5 h-4.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Adresse</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $property->address ?? '-' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $property->city ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- Caractéristiques --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Caractéristiques</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-slate-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400">Surface</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $property->surface ? $property->surface . ' m²' : '-' }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400">Pièces</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $property->rooms ?? '-' }}</p>
                </div>
                @if($property->niveau)
                <div class="bg-slate-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400">Niveau</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $property->niveau }}</p>
                </div>
                @endif
                @if($property->numero_porte)
                <div class="bg-slate-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400">N° Porte</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $property->numero_porte }}</p>
                </div>
                @endif
                <div class="bg-slate-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400">Clés</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $property->nb_keys ?? '-' }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400">Climatiseurs</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $property->nb_clim ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Compteurs --}}
        @if($property->cie_meter_number || $property->sodeci_meter_number)
        <div class="px-6 py-5 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Compteurs</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($property->cie_meter_number)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Compteur CIE</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $property->cie_meter_number }}</p>
                    </div>
                </div>
                @endif
                @if($property->sodeci_meter_number)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Compteur SODECI</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $property->sodeci_meter_number }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Description --}}
        @if($property->description)
        <div class="px-6 py-4 bg-slate-50/30">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Description</p>
            <p class="text-sm text-gray-700">{{ $property->description }}</p>
        </div>
        @endif
    </div>

    {{-- Map --}}
    @if($property->latitude && $property->longitude)
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Localisation</h3>
            <div id="show-map" style="height: 300px; position: relative; z-index: 0;" class="rounded-lg border border-gray-200 overflow-hidden"></div>
            <p class="mt-2 text-xs text-gray-500">Coordonnées : {{ number_format($property->latitude, 7) }}, {{ number_format($property->longitude, 7) }}</p>
        </div>
        @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        @endpush
        @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('show-map').setView([{{ $property->latitude }}, {{ $property->longitude }}], 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19, attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            L.marker([{{ $property->latitude }}, {{ $property->longitude }}]).addTo(map);
        });
        </script>
        @endpush
    @endif

    {{-- Photo Gallery --}}
    @if(!empty($property->photos))
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6" x-data="photoGallery()" x-cloak>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Photos ({{ count($property->photos) }})</h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($property->photos as $index => $photo)
                    <div class="relative group rounded-lg overflow-hidden border border-gray-200 cursor-pointer"
                         @click="openLightbox({{ $index }})">
                        <img src="{{ asset('storage/' . $photo) }}" alt="Photo {{ $index + 1 }}"
                             class="w-full h-32 object-cover transition group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition flex items-center justify-center pointer-events-none">
                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </div>
                        @can('update', $property)
                            <button type="button"
                                    @click.stop="deletePhoto({{ $index }})"
                                    class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 flex items-center justify-center text-white text-xs opacity-0 group-hover:opacity-100 transition">
                                &times;
                            </button>
                        @endcan
                    </div>
                @endforeach
            </div>

            {{-- Lightbox Modal --}}
            <div x-show="lightboxOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" @keydown.escape.window="closeLightbox()" @click.self="closeLightbox()">
                <button @click="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <button x-show="photos.length > 1" @click="prev()" class="absolute left-4 text-white hover:text-gray-300 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <img :src="currentPhoto" class="max-h-[85vh] max-w-[90vw] rounded-lg shadow-2xl" alt="">
                <button x-show="photos.length > 1" @click="next()" class="absolute right-4 text-white hover:text-gray-300 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <script>
            function photoGallery() {
                return {
                    photos: @json(collect($property->photos)->map(fn($p) => asset('storage/' . $p))->values()),
                    lightboxOpen: false,
                    currentIndex: 0,
                    get currentPhoto() { return this.photos[this.currentIndex] || ''; },
                    openLightbox(idx) { this.currentIndex = idx; this.lightboxOpen = true; },
                    closeLightbox() { this.lightboxOpen = false; },
                    prev() { this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length; },
                    next() { this.currentIndex = (this.currentIndex + 1) % this.photos.length; },
                    async deletePhoto(index) {
                        if (!confirm('Supprimer cette photo ?')) return;
                        try {
                            const res = await fetch(`{{ url('/properties/' . $property->id . '/photos') }}/` + index, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                },
                            });
                            if (res.ok) window.location.reload();
                        } catch (e) {
                            alert('Erreur lors de la suppression.');
                        }
                    }
                };
            }
        </script>
    @endif

    {{-- Current Lease --}}
    @if($property->activeLease)
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Bail actif</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Locataire</p>
                            <a href="{{ route('tenants.show', $property->activeLease->tenant) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-800 transition">
                                {{ $property->activeLease->tenant->full_name }}
                            </a>
                        </div>
                    </div>
                    <div class="bg-brand-50/60 rounded-xl p-3 border border-brand-100">
                        <p class="text-xs text-brand-500">Loyer mensuel</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($property->activeLease->rent_amount, 0, ',', ' ') }} <span class="text-xs font-medium text-gray-500">FCFA</span></p>
                    </div>
                    <div class="pl-4 border-l-2 border-brand-200">
                        <p class="text-xs text-gray-400">Début</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $property->activeLease->start_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="pl-4 border-l-2 border-orange-200">
                        <p class="text-xs text-gray-400">Fin</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $property->activeLease->end_date->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Lease History --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Historique des baux</h3>
        </div>

        @if($property->leases->count())
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Locataire</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Loyer</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Début</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Fin</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-gray-400">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($property->leases as $lease)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $lease->tenant->full_name ?? '-' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ number_format($lease->rent_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $lease->start_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">{{ $lease->end_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($lease->status === 'actif')
                                    <x-badge type="success">Actif</x-badge>
                                @elseif($lease->status === 'termine')
                                    <x-badge type="default">Terminé</x-badge>
                                @elseif($lease->status === 'resilie')
                                    <x-badge type="danger">Résilié</x-badge>
                                @else
                                    <x-badge type="warning">{{ ucfirst($lease->status) }}</x-badge>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <x-empty-state message="Aucun bail pour ce bien." />
        @endif
    </div>

    {{-- Edit Property Modal --}}
    @can('update', $property)
        <x-form-modal name="edit-property-{{ $property->id }}" title="Modifier le bien {{ $property->reference }}" :action="route('properties.update', $property)" method="PUT" maxWidth="3xl" :hasFiles="true" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>' iconColor="text-emerald-500">
            @include('properties._form', ['property' => $property])
        </x-form-modal>
    @endcan
@endsection
