@php $p = $property; @endphp
<div class="space-y-6">
    {{-- Informations generales --}}
    <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">INFORMATIONS GÉNÉRALES</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">SCI</label>
                @if($activeSci ?? null)
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $activeSci->name }}</p>
                    <input type="hidden" name="sci_id" value="{{ $activeSci->id }}">
                @else
                    <select name="sci_id" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <option value="">-- Selectionner une SCI --</option>
                        @foreach(\App\Models\Sci::all() as $sci)
                            <option value="{{ $sci->id }}" {{ ($p->sci_id ?? '') == $sci->id ? 'selected' : '' }}>{{ $sci->name }}</option>
                        @endforeach
                    </select>
                @endif
                <template x-if="errors.sci_id"><p class="mt-1 text-sm text-red-600" x-text="errors.sci_id[0]"></p></template>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Reference</label>
                <input type="text" name="reference" value="{{ $p->reference ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.reference"><p class="mt-1 text-sm text-red-600" x-text="errors.reference[0]"></p></template>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    <option value="">-- Selectionner --</option>
                    @foreach(['appartement','maison','studio','bureau','boutique','entrepot','terrain','autre'] as $t)
                        <option value="{{ $t }}" {{ ($p->type ?? '') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
                <template x-if="errors.type"><p class="mt-1 text-sm text-red-600" x-text="errors.type[0]"></p></template>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                <textarea name="address" rows="2" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ $p->address ?? '' }}</textarea>
                <template x-if="errors.address"><p class="mt-1 text-sm text-red-600" x-text="errors.address[0]"></p></template>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ville</label>
                <input type="text" name="city" value="{{ $p->city ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.city"><p class="mt-1 text-sm text-red-600" x-text="errors.city[0]"></p></template>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Statut</label>
                <select name="status" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    <option value="">-- Selectionner --</option>
                    <option value="disponible" {{ ($p->status ?? '') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="occupe" {{ ($p->status ?? '') === 'occupe' ? 'selected' : '' }}>Occupe</option>
                    <option value="travaux" {{ ($p->status ?? '') === 'travaux' ? 'selected' : '' }}>En travaux</option>
                </select>
                <template x-if="errors.status"><p class="mt-1 text-sm text-red-600" x-text="errors.status[0]"></p></template>
            </div>
        </div>
    </div>

    {{-- Caracteristiques --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">CARACTÉRISTIQUES</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Surface (m2)</label>
                <input type="number" step="0.01" name="surface" value="{{ $p->surface ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.surface"><p class="mt-1 text-sm text-red-600" x-text="errors.surface[0]"></p></template>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre de pieces</label>
                <input type="number" name="rooms" value="{{ $p->rooms ?? '' }}" min="0" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.rooms"><p class="mt-1 text-sm text-red-600" x-text="errors.rooms[0]"></p></template>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre de cles</label>
                <input type="number" name="nb_keys" value="{{ $p->nb_keys ?? '' }}" min="0" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre de climatiseurs</label>
                <input type="number" name="nb_clim" value="{{ $p->nb_clim ?? '' }}" min="0" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Niveau / Etage</label>
                <input type="text" name="niveau" value="{{ $p->niveau ?? '' }}" placeholder="Ex: RDC, 1er, 2e..." class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.niveau"><p class="mt-1 text-sm text-red-600" x-text="errors.niveau[0]"></p></template>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">N° Appartement</label>
                <input type="text" name="numero_porte" value="{{ $p->numero_porte ?? '' }}" placeholder="Ex: A12, 203..." class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.numero_porte"><p class="mt-1 text-sm text-red-600" x-text="errors.numero_porte[0]"></p></template>
            </div>
        </div>
    </div>

    {{-- Localisation sur carte --}}
    <div class="border-t border-gray-100 pt-5 mt-5" x-data="propertyMap({{ json_encode($p->latitude ?? null) }}, {{ json_encode($p->longitude ?? null) }}, '{{ $p ? 'edit-'.$p->id : 'create' }}')" x-init="initMap()">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">LOCALISATION</p>
        <p class="text-xs text-gray-500 mb-2">Cliquez sur la carte ou deplacez le marqueur pour positionner le bien avec precision.</p>

        {{-- Search bar --}}
        <div class="relative mb-3">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" x-ref="searchInput"
                   @keydown.enter.prevent="searchAddress()"
                   placeholder="Rechercher une adresse..."
                   class="block w-full pl-9 pr-24 py-2 rounded-lg border border-gray-300 bg-transparent text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            <button type="button" @click="searchAddress()" class="absolute inset-y-0 right-0 flex items-center px-3 text-sm font-medium text-brand-700 hover:text-brand-900">
                Rechercher
            </button>
        </div>

        {{-- Map container --}}
        <div x-ref="mapContainer" style="height: 300px; position: relative; z-index: 0;" class="rounded-lg border border-gray-200 overflow-hidden"></div>

        {{-- Buttons --}}
        <div class="mt-3 flex flex-wrap gap-2">
            <button type="button" @click="geolocate()"
                    :disabled="geoLoading"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-brand-700 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition disabled:opacity-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-text="geoLoading ? 'Localisation...' : 'Ma position'"></span>
            </button>
            <button type="button" @click="clearPosition()"
                    x-show="lat !== null"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Effacer
            </button>
        </div>
        <p x-show="geoError" x-text="geoError" class="mt-1 text-sm text-red-600"></p>

        {{-- Coordinates display --}}
        <div x-show="lat !== null" class="mt-2 text-xs text-gray-500">
            Coordonnees : <span x-text="lat ? lat.toFixed(7) : ''"></span>, <span x-text="lng ? lng.toFixed(7) : ''"></span>
        </div>

        {{-- Hidden inputs --}}
        <input type="hidden" name="latitude" :value="lat">
        <input type="hidden" name="longitude" :value="lng">
        <template x-if="errors.latitude"><p class="mt-1 text-sm text-red-600" x-text="errors.latitude[0]"></p></template>
        <template x-if="errors.longitude"><p class="mt-1 text-sm text-red-600" x-text="errors.longitude[0]"></p></template>
    </div>

    {{-- Compteurs --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">COMPTEURS</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Compteur CIE</label>
                <input type="text" name="cie_meter_number" value="{{ $p->cie_meter_number ?? '' }}" placeholder="N° compteur CIE"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.cie_meter_number"><p class="mt-1 text-sm text-red-600" x-text="errors.cie_meter_number[0]"></p></template>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Compteur SODECI</label>
                <input type="text" name="sodeci_meter_number" value="{{ $p->sodeci_meter_number ?? '' }}" placeholder="N° compteur SODECI"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.sodeci_meter_number"><p class="mt-1 text-sm text-red-600" x-text="errors.sodeci_meter_number[0]"></p></template>
            </div>
        </div>
    </div>

    {{-- Photos --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">PHOTOS</p>
        <x-multi-photo-upload :existing="$p->photos ?? []" />
    </div>
</div>

@pushOnce('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>.leaflet-container{font-family:inherit;}</style>
@endPushOnce

@pushOnce('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
function propertyMap(initLat, initLng, mapId) {
    return {
        lat: initLat,
        lng: initLng,
        map: null,
        marker: null,
        geoLoading: false,
        geoError: '',

        initMap() {
            this.$nextTick(() => {
                const container = this.$refs.mapContainer;
                if (!container || container._leaflet_id) return;

                const defaultLat = this.lat || 5.3364;
                const defaultLng = this.lng || -4.0267;
                const defaultZoom = this.lat ? 17 : 13;

                this.map = L.map(container, { scrollWheelZoom: true }).setView([defaultLat, defaultLng], defaultZoom);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(this.map);

                if (this.lat && this.lng) {
                    this.placeMarker(this.lat, this.lng, false);
                }

                this.map.on('click', (e) => {
                    this.placeMarker(e.latlng.lat, e.latlng.lng, true);
                });

                // Fix map size when modal opens (tiles may not load otherwise)
                setTimeout(() => { this.map.invalidateSize(); }, 300);
                const observer = new MutationObserver(() => {
                    if (container.offsetHeight > 0) this.map.invalidateSize();
                });
                observer.observe(container.closest('[x-show], [x-cloak], dialog, .modal') || container.parentElement, {
                    attributes: true, attributeFilter: ['class', 'style']
                });
            });
        },

        placeMarker(lat, lng, animate) {
            this.lat = lat;
            this.lng = lng;
            if (this.marker) {
                this.marker.setLatLng([lat, lng]);
            } else {
                this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
                this.marker.on('dragend', () => {
                    const pos = this.marker.getLatLng();
                    this.lat = pos.lat;
                    this.lng = pos.lng;
                });
            }
            if (animate) {
                this.map.setView([lat, lng], Math.max(this.map.getZoom(), 17));
            }
        },

        geolocate() {
            this.geoError = '';
            if (!navigator.geolocation) {
                this.geoError = 'La geolocalisation n\'est pas supportee par ce navigateur.';
                return;
            }
            this.geoLoading = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.placeMarker(pos.coords.latitude, pos.coords.longitude, true);
                    this.map.setView([pos.coords.latitude, pos.coords.longitude], 17);
                    this.geoLoading = false;
                },
                (err) => {
                    this.geoError = 'Impossible d\'obtenir la position : ' + err.message;
                    this.geoLoading = false;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        clearPosition() {
            this.lat = null;
            this.lng = null;
            if (this.marker) {
                this.map.removeLayer(this.marker);
                this.marker = null;
            }
        },

        async searchAddress() {
            const query = this.$refs.searchInput.value.trim();
            if (!query) return;
            this.geoError = '';
            try {
                const res = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query), {
                    headers: { 'Accept-Language': 'fr' }
                });
                const data = await res.json();
                if (data.length === 0) {
                    this.geoError = 'Aucun resultat pour cette adresse.';
                    return;
                }
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                this.placeMarker(lat, lng, true);
                this.map.setView([lat, lng], 17);
            } catch (e) {
                this.geoError = 'Erreur lors de la recherche d\'adresse.';
            }
        }
    };
}
</script>
@endPushOnce
