@php $u = $user ?? null; $isEdit = !is_null($u); @endphp
<div class="space-y-0">

    {{-- Section 1: Photo de profil --}}
    <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
            </svg>
            PHOTO DE PROFIL
        </p>
        <div class="flex items-center gap-5">
            <div class="shrink-0">
                @if($u && $u->avatar_path)
                    <img src="{{ asset('storage/' . $u->avatar_path) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div class="w-16 h-16 rounded-full bg-brand-100 flex items-center justify-center border-2 border-gray-200">
                        <span class="text-xl font-bold text-brand-600">{{ mb_substr($u->name ?? '?', 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Avatar</label>
                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                       class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                <p class="mt-1 text-xs text-gray-400">JPG, PNG ou WebP (max 2 Mo)</p>
            </div>
        </div>
    </div>

    {{-- Section 2: Identifiants --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
            </svg>
            IDENTIFIANTS
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" name="name" value="{{ $u->name ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.name"><p class="mt-1 text-sm text-red-600" x-text="errors.name[0]"></p></template>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">E-mail</label>
                <input type="email" name="email" value="{{ $u->email ?? '' }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.email"><p class="mt-1 text-sm text-red-600" x-text="errors.email[0]"></p></template>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Mot de passe {{ $isEdit ? '(laisser vide pour ne pas changer)' : '' }}</label>
                <input type="password" name="password" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <template x-if="errors.password"><p class="mt-1 text-sm text-red-600" x-text="errors.password[0]"></p></template>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
        </div>
    </div>

    {{-- Section 3: Role & Acces --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
            </svg>
            ROLE & ACCES
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    <option value="">-- Selectionner --</option>
                    <option value="super_admin" {{ ($u->role ?? '') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="gestionnaire" {{ ($u->role ?? '') === 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
                    <option value="lecture_seule" {{ ($u->role ?? '') === 'lecture_seule' ? 'selected' : '' }}>Lecture seule</option>
                </select>
                <template x-if="errors.role"><p class="mt-1 text-sm text-red-600" x-text="errors.role[0]"></p></template>
            </div>

            <div class="flex items-center justify-between pt-6 px-1" x-data="{ enabled: {{ ($u->is_active ?? true) ? 'true' : 'false' }} }">
                <input type="hidden" name="is_active" :value="enabled ? '1' : '0'">
                <span class="text-sm font-medium text-gray-700">Compte actif</span>
                <button type="button" role="switch" :aria-checked="enabled" @click="enabled = !enabled"
                        :class="enabled ? 'bg-brand-600' : 'bg-gray-200'"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Section 4: SCIs affectees --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M3.75 3v18m16.5-18v18M5.25 6h.008v.008H5.25V6zm0 3h.008v.008H5.25V9zm0 3h.008v.008H5.25V12zm0 3h.008v.008H5.25V15zm6-9h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12zm0 3h.008v.008h-.008V15zm6-9h.008v.008h-.008V6zm0 3h.008v.008h-.008V9zm0 3h.008v.008h-.008V12zm0 3h.008v.008h-.008V15z" />
            </svg>
            SCIS AFFECTEES
        </p>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">SCIs affectees</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 p-4 border border-gray-200 rounded-lg max-h-48 overflow-y-auto">
                @foreach(\App\Models\Sci::all() as $sci)
                    <label class="flex items-center text-sm">
                        <input type="checkbox" name="sci_ids[]" value="{{ $sci->id }}"
                               {{ in_array($sci->id, $u ? $u->scis->pluck('id')->toArray() : []) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500">
                        <span class="ml-2 text-gray-700">{{ $sci->name }}</span>
                    </label>
                @endforeach
            </div>
            <template x-if="errors.sci_ids"><p class="mt-1 text-sm text-red-600" x-text="errors.sci_ids[0]"></p></template>
        </div>
    </div>

</div>
