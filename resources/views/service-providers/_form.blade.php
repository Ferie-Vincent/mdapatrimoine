@php $p = $provider ?? new \App\Models\ServiceProvider(); @endphp

<div x-data="{ category: '{{ $p->category ?? 'artisan' }}' }">

    {{-- Section: IDENTITÉ --}}
    <div class="mb-2">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block w-3.5 h-3.5 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Identite
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(!$provider)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">SCI</label>
                    @if($activeSci ?? null)
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $activeSci->name }}</p>
                        <input type="hidden" name="sci_id" value="{{ $activeSci->id }}">
                    @else
                        <select name="sci_id" required class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                            <option value="">-- Selectionner une SCI --</option>
                            @foreach(\App\Models\Sci::all() as $sci)
                                <option value="{{ $sci->id }}">{{ $sci->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    <template x-if="errors.sci_id"><p class="mt-1 text-sm text-red-600" x-text="errors.sci_id[0]"></p></template>
                </div>
            @endif

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ $p->name }}" required
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.name"><p class="mt-1 text-sm text-red-600" x-text="errors.name[0]"></p></template>
            </div>
        </div>
    </div>

    {{-- Section: CATÉGORIE & SPÉCIALITÉ --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block w-3.5 h-3.5 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
            Categorie & Specialite
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Categorie <span class="text-red-500">*</span></label>
                <select name="category" x-model="category" required
                        class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    <option value="artisan" {{ $p->category === 'artisan' ? 'selected' : '' }}>Artisan</option>
                    <option value="manoeuvre" {{ $p->category === 'manoeuvre' ? 'selected' : '' }}>Manoeuvre</option>
                    <option value="plombier" {{ $p->category === 'plombier' ? 'selected' : '' }}>Plombier</option>
                    <option value="electricien" {{ $p->category === 'electricien' ? 'selected' : '' }}>Electricien</option>
                    <option value="peintre" {{ $p->category === 'peintre' ? 'selected' : '' }}>Peintre</option>
                    <option value="menuisier" {{ $p->category === 'menuisier' ? 'selected' : '' }}>Menuisier</option>
                    <option value="macon" {{ $p->category === 'macon' ? 'selected' : '' }}>Macon</option>
                    <option value="serrurier" {{ $p->category === 'serrurier' ? 'selected' : '' }}>Serrurier</option>
                    <option value="climatiseur" {{ $p->category === 'climatiseur' ? 'selected' : '' }}>Climatiseur</option>
                    <option value="autre" {{ $p->category === 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
                <template x-if="errors.category"><p class="mt-1 text-sm text-red-600" x-text="errors.category[0]"></p></template>
            </div>

            <div x-show="category === 'autre'" x-transition>
                <label class="block text-sm font-medium text-gray-700">Categorie personnalisee <span class="text-red-500">*</span></label>
                <input type="text" name="custom_category" value="{{ $p->custom_category }}"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.custom_category"><p class="mt-1 text-sm text-red-600" x-text="errors.custom_category[0]"></p></template>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Specialite</label>
                <input type="text" name="specialty" value="{{ $p->specialty }}"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Entreprise</label>
                <input type="text" name="company" value="{{ $p->company }}"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
        </div>
    </div>

    {{-- Section: COORDONNÉES --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block w-3.5 h-3.5 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            Coordonnees
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Telephone</label>
                <input type="text" name="phone" value="{{ $p->phone }}"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Telephone secondaire</label>
                <input type="text" name="phone_secondary" value="{{ $p->phone_secondary }}"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ $p->email }}"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.email"><p class="mt-1 text-sm text-red-600" x-text="errors.email[0]"></p></template>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                <input type="text" name="address" value="{{ $p->address }}"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
        </div>
    </div>

    {{-- Section: NOTES --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block w-3.5 h-3.5 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Notes
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" rows="3"
                          class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ $p->notes }}</textarea>
            </div>

            @if($provider)
                <div class="md:col-span-2 flex items-center justify-between px-1 pt-2" x-data="{ enabled: {{ $p->is_active ? 'true' : 'false' }} }">
                    <input type="hidden" name="is_active" :value="enabled ? '1' : '0'">
                    <span class="text-sm font-medium text-gray-700">Prestataire actif</span>
                    <button type="button" role="switch" :aria-checked="enabled" @click="enabled = !enabled"
                            :class="enabled ? 'bg-brand-600' : 'bg-gray-200'"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                </div>
            @endif
        </div>
    </div>

</div>
