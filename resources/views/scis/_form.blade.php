@php $s = $sci ?? null; @endphp
<div class="space-y-0">

    {{-- Section 1: Informations generales --}}
    <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M3.75 3v18m16.5-18v18M5.25 6h.008v.008H5.25V6zm0 3h.008v.008H5.25V9zm0 3h.008v.008H5.25V12zm0 3h.008v.008H5.25V15zm6-9h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12zm0 3h.008v.008h-.008V15zm6-9h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12zm0 3h.008v.008h-.008V15z" />
            </svg>
            INFORMATIONS GENERALES
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ $s->name ?? '' }}" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.name"><p class="mt-1 text-sm text-red-600" x-text="errors.name[0]"></p></template>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">RCCM</label>
                <input type="text" name="rccm" value="{{ $s->rccm ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.rccm"><p class="mt-1 text-sm text-red-600" x-text="errors.rccm[0]"></p></template>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">IFU</label>
                <input type="text" name="ifu" value="{{ $s->ifu ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.ifu"><p class="mt-1 text-sm text-red-600" x-text="errors.ifu[0]"></p></template>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                <textarea name="address" rows="3" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ $s->address ?? '' }}</textarea>
                <template x-if="errors.address"><p class="mt-1 text-sm text-red-600" x-text="errors.address[0]"></p></template>
            </div>
        </div>
    </div>

    {{-- Section 2: Coordonnees --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
            COORDONNEES
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Telephone</label>
                <input type="text" name="phone" value="{{ $s->phone ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.phone"><p class="mt-1 text-sm text-red-600" x-text="errors.phone[0]"></p></template>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ $s->email ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.email"><p class="mt-1 text-sm text-red-600" x-text="errors.email[0]"></p></template>
            </div>
        </div>
    </div>

    {{-- Section 3: Informations bancaires --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
            </svg>
            INFORMATIONS BANCAIRES
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Banque</label>
                <input type="text" name="bank_name" value="{{ $s->bank_name ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">IBAN</label>
                <input type="text" name="bank_iban" value="{{ $s->bank_iban ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
        </div>
    </div>

</div>
