@php $c = $contract ?? new \App\Models\ProviderContract(); @endphp

<div>

    {{-- Section: PARTIES --}}
    <div class="mb-2">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block w-3.5 h-3.5 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Parties
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- SCI --}}
            @if($activeSci ?? null)
                <input type="hidden" name="sci_id" value="{{ $activeSci->id }}">
            @else
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">SCI <span class="text-red-500">*</span></label>
                    <select name="sci_id" required
                            class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <option value="">-- Selectionner une SCI --</option>
                        @foreach(\App\Models\Sci::all() as $sci)
                            <option value="{{ $sci->id }}" {{ $c->sci_id == $sci->id ? 'selected' : '' }}>{{ $sci->name }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors.sci_id"><p class="mt-1 text-sm text-red-600" x-text="errors.sci_id[0]"></p></template>
                </div>
            @endif

            {{-- Provider --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Prestataire <span class="text-red-500">*</span></label>
                <select name="service_provider_id" required
                        class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    <option value="">-- Selectionner un prestataire --</option>
                    @foreach($allProviders as $prov)
                        <option value="{{ $prov->id }}" {{ $c->service_provider_id == $prov->id ? 'selected' : '' }}>{{ $prov->name }}</option>
                    @endforeach
                </select>
                <template x-if="errors.service_provider_id"><p class="mt-1 text-sm text-red-600" x-text="errors.service_provider_id[0]"></p></template>
            </div>
        </div>
    </div>

    {{-- Section: CONTRAT --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block w-3.5 h-3.5 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Contrat
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Title --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Titre du contrat <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ $c->title }}" required
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.title"><p class="mt-1 text-sm text-red-600" x-text="errors.title[0]"></p></template>
            </div>

            {{-- Amount --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Montant (FCFA) <span class="text-red-500">*</span></label>
                <x-money-input name="amount" :value="$c->amount ?? ''" :required="true" />
                <template x-if="errors.amount"><p class="mt-1 text-sm text-red-600" x-text="errors.amount[0]"></p></template>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Statut <span class="text-red-500">*</span></label>
                <select name="status" required
                        class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    <option value="actif" {{ ($c->status ?? 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="termine" {{ $c->status === 'termine' ? 'selected' : '' }}>Termine</option>
                    <option value="annule" {{ $c->status === 'annule' ? 'selected' : '' }}>Annule</option>
                </select>
                <template x-if="errors.status"><p class="mt-1 text-sm text-red-600" x-text="errors.status[0]"></p></template>
            </div>
        </div>
    </div>

    {{-- Section: PÉRIODE --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block w-3.5 h-3.5 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Periode
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Start date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Date de debut <span class="text-red-500">*</span></label>
                <input type="date" name="start_date" value="{{ $c->start_date?->format('Y-m-d') }}" required
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.start_date"><p class="mt-1 text-sm text-red-600" x-text="errors.start_date[0]"></p></template>
            </div>

            {{-- End date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Date de fin</label>
                <input type="date" name="end_date" value="{{ $c->end_date?->format('Y-m-d') }}"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.end_date"><p class="mt-1 text-sm text-red-600" x-text="errors.end_date[0]"></p></template>
            </div>
        </div>
    </div>

    {{-- Section: DESCRIPTION --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block w-3.5 h-3.5 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
            Description
        </p>
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3"
                      class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ $c->description }}</textarea>
        </div>
    </div>

</div>
