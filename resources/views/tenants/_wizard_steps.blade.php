{{-- Step 1: Identite --}}
<div x-show="currentStep === 0" data-step="0">
    {{-- Info card --}}
    <div class="flex items-center gap-3 rounded-xl bg-blue-50/60 border border-blue-100 px-4 py-3 mb-6">
        <svg class="h-5 w-5 text-blue-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
        <p class="text-sm text-blue-600">Renseignez les informations personnelles et les coordonnees du locataire.</p>
    </div>

    <input type="hidden" name="sci_id" value="{{ $t->sci_id }}">

    {{-- Informations personnelles --}}
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
        <svg class="inline-block h-3.5 w-3.5 mr-1 -mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
        Informations personnelles
    </p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Prenom</label>
            <input type="text" name="first_name" value="{{ $t->first_name }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            <template x-if="errors.first_name"><p class="mt-1 text-sm text-red-600" x-text="errors.first_name[0]"></p></template>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" value="{{ $t->last_name }}" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            <template x-if="errors.last_name"><p class="mt-1 text-sm text-red-600" x-text="errors.last_name[0]"></p></template>
        </div>
    </div>

    {{-- Coordonnees --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block h-3.5 w-3.5 mr-1 -mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
            Coordonnees
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ $t->email }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Telephone <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="{{ $t->phone }}" required class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Telephone secondaire</label>
                <input type="text" name="phone_secondary" value="{{ $t->phone_secondary }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    <svg class="inline-block w-3.5 h-3.5 mr-1 -mt-0.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.611.611l4.458-1.495A11.96 11.96 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.339 0-4.508-.782-6.243-2.1l-.436-.338-2.842.953.953-2.842-.338-.436A9.956 9.956 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
                    WhatsApp
                </label>
                <input type="text" name="whatsapp_phone" value="{{ $t->whatsapp_phone }}" placeholder="+225XXXXXXXXXX"
                       class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                <p class="mt-1 text-xs text-gray-400">Numero avec indicatif pays (ex: +225)</p>
                <template x-if="errors.whatsapp_phone"><p class="mt-1 text-sm text-red-600" x-text="errors.whatsapp_phone[0]"></p></template>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                <textarea name="address" rows="2" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ $t->address }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Step 2: Piece d'identite --}}
<div x-show="currentStep === 1" data-step="1">
    <div class="space-y-6">
        {{-- Informations de la piece --}}
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
                <svg class="inline-block h-3.5 w-3.5 mr-1 -mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" /></svg>
                Informations de la piece
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type de piece</label>
                    <select name="id_type" class="mt-1.5 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                        <option value="">-- Selectionner --</option>
                        <option value="CNI" {{ $t->id_type === 'CNI' ? 'selected' : '' }}>CNI</option>
                        <option value="Passeport" {{ $t->id_type === 'Passeport' ? 'selected' : '' }}>Passeport</option>
                        <option value="Permis" {{ $t->id_type === 'Permis' ? 'selected' : '' }}>Permis de conduire</option>
                        <option value="Autre" {{ $t->id_type === 'Autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Numero</label>
                    <input type="text" name="id_number" value="{{ $t->id_number }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" name="id_expiration" value="{{ $t->id_expiration }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
            </div>
        </div>

        {{-- Documents --}}
        <div class="border-t border-gray-100 pt-5 mt-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
                <svg class="inline-block h-3.5 w-3.5 mr-1 -mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                Documents
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-file-upload name="id_file" label="Recto de la piece" accept="image/*,.pdf" hint="Image ou PDF (max 5 Mo)" :current="$t->id_file_path ? basename($t->id_file_path) : null" />
                </div>
                <div>
                    <x-file-upload name="id_file_verso" label="Verso de la piece" accept="image/*,.pdf" hint="Image ou PDF (max 5 Mo)" :current="$t->id_file_verso_path ? basename($t->id_file_verso_path) : null" />
                    <template x-if="errors.id_file_verso"><p class="mt-1 text-sm text-red-600" x-text="errors.id_file_verso[0]"></p></template>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Step 3: Professionnel --}}
<div x-show="currentStep === 2" data-step="2">
    <div class="space-y-6">
        {{-- Situation professionnelle --}}
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
                <svg class="inline-block h-3.5 w-3.5 mr-1 -mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                Situation professionnelle
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Profession</label>
                    <input type="text" name="profession" value="{{ $t->profession }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Employeur</label>
                    <input type="text" name="employer" value="{{ $t->employer }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
            </div>
        </div>

        {{-- Justificatif --}}
        <div class="border-t border-gray-100 pt-5 mt-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
                <svg class="inline-block h-3.5 w-3.5 mr-1 -mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                Justificatif
            </p>
            <x-file-upload name="payment_receipt" label="Justificatif de paiement" accept="image/*,.pdf" hint="Photo ou PDF (max 5 Mo)" :current="$t->payment_receipt_path ? basename($t->payment_receipt_path) : null" />
            <template x-if="errors.payment_receipt"><p class="mt-1 text-sm text-red-600" x-text="errors.payment_receipt[0]"></p></template>
        </div>
    </div>
</div>

{{-- Step 4: Contact d'urgence --}}
<div x-show="currentStep === 3" data-step="3">
    <div class="bg-amber-50/50 rounded-xl p-5 border border-amber-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block h-3.5 w-3.5 mr-1 -mt-0.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
            Contact d'urgence
        </p>
        <p class="text-sm text-gray-500 mb-5">Personne a contacter en cas d'urgence.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nom du contact</label>
                <input type="text" name="emergency_contact_name" value="{{ $t->emergency_contact_name }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Telephone du contact</label>
                <input type="text" name="emergency_contact_phone" value="{{ $t->emergency_contact_phone }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
        </div>
    </div>
</div>

{{-- Step 5: Garant --}}
<div x-show="currentStep === 4" data-step="4">
    {{-- Identite du garant --}}
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
        <svg class="inline-block h-3.5 w-3.5 mr-1 -mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
        Identite du garant
    </p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nom du garant</label>
            <input type="text" name="guarantor_name" value="{{ $t->guarantor_name }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Telephone du garant</label>
            <input type="text" name="guarantor_phone" value="{{ $t->guarantor_phone }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
        </div>
    </div>

    {{-- Informations complementaires --}}
    <div class="border-t border-gray-100 pt-5 mt-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            <svg class="inline-block h-3.5 w-3.5 mr-1 -mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
            Informations complementaires
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">N° piece du garant</label>
                <input type="text" name="guarantor_id_number" value="{{ $t->guarantor_id_number }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Profession du garant</label>
                <input type="text" name="guarantor_profession" value="{{ $t->guarantor_profession }}" class="mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Adresse du garant</label>
            <textarea name="guarantor_address" rows="2" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ $t->guarantor_address }}</textarea>
        </div>
    </div>
</div>
