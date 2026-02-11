@extends('layouts.app')

@section('title', 'Parametres')

@section('content')
<div x-data="{ tab: 'relances' }" class="space-y-6">

    {{-- Success message --}}
    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 p-4">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-6 -mb-px">
            <button @click="tab = 'relances'" :class="tab === 'relances' ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium transition whitespace-nowrap">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Relances
            </button>
            <button @click="tab = 'twilio'" :class="tab === 'twilio' ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium transition whitespace-nowrap">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Twilio (WhatsApp / SMS)
            </button>
            <button @click="tab = 'baux'" :class="tab === 'baux' ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium transition whitespace-nowrap">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Valeurs par defaut baux
            </button>
        </nav>
    </div>

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ============================================================ --}}
        {{-- TAB: Relances --}}
        {{-- ============================================================ --}}
        <div x-show="tab === 'relances'" x-cloak class="space-y-6">

            {{-- Signature entreprise --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Signature entreprise</h3>
                <div>
                    <label for="reminder_company_signature" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nom affiché dans les messages</label>
                    <input type="text" name="reminder_company_signature" id="reminder_company_signature"
                           value="{{ old('reminder_company_signature', $settings['reminder_company_signature'] ?? 'MDA Patrimoine') }}"
                           class="block w-full max-w-md rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                    @error('reminder_company_signature') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Placeholders help --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-xs font-semibold text-blue-800 mb-2">Variables disponibles dans les messages :</p>
                <div class="flex flex-wrap gap-2">
                    <code class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-xs font-mono">{tenantName}</code>
                    <code class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-xs font-mono">{month}</code>
                    <code class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-xs font-mono">{propertyRef}</code>
                    <code class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-xs font-mono">{amount}</code>
                </div>
            </div>

            @php
                $levels = [
                    1 => ['label' => 'Niveau 1 — Courtois', 'color' => 'blue', 'desc' => 'Premier rappel poli'],
                    2 => ['label' => 'Niveau 2 — Ferme', 'color' => 'orange', 'desc' => 'Deuxieme rappel, ton plus ferme'],
                    3 => ['label' => 'Niveau 3 — Mise en demeure', 'color' => 'red', 'desc' => 'Dernier avertissement avant poursuites'],
                ];
            @endphp

            @foreach($levels as $lvl => $info)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $info['color'] }}-100 text-{{ $info['color'] }}-700">
                            Niveau {{ $lvl }}
                        </span>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $info['label'] }}</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">{{ $info['desc'] }}</p>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="reminder_level{{ $lvl }}_days" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Delai (jours apres echeance)</label>
                            <div class="relative">
                                <input type="number" name="reminder_level{{ $lvl }}_days" id="reminder_level{{ $lvl }}_days" min="1" max="90"
                                       value="{{ old("reminder_level{$lvl}_days", $settings["reminder_level{$lvl}_days"] ?? ($lvl === 1 ? 3 : ($lvl === 2 ? 7 : 15))) }}"
                                       class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2 pr-12">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 text-xs pointer-events-none">jours</span>
                            </div>
                            @error("reminder_level{$lvl}_days") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="lg:col-span-3">
                            <label for="reminder_level{{ $lvl }}_message" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Template du message</label>
                            <textarea name="reminder_level{{ $lvl }}_message" id="reminder_level{{ $lvl }}_message" rows="4"
                                      class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2 resize-y">{{ old("reminder_level{$lvl}_message", $settings["reminder_level{$lvl}_message"] ?? '') }}</textarea>
                            @error("reminder_level{$lvl}_message") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ============================================================ --}}
        {{-- TAB: Twilio --}}
        {{-- ============================================================ --}}
        <div x-show="tab === 'twilio'" x-cloak class="space-y-6">

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-semibold text-gray-900">Configuration Twilio</h3>
                    @php
                        $twilioConfigured = !empty($settings['twilio_sid'] ?? '') && !empty($settings['twilio_auth_token'] ?? '');
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $twilioConfigured ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        <span class="w-2 h-2 rounded-full {{ $twilioConfigured ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $twilioConfigured ? 'Configure' : 'Non configure' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="twilio_sid" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Account SID</label>
                        <input type="text" name="twilio_sid" id="twilio_sid"
                               value="{{ old('twilio_sid', $settings['twilio_sid'] ?? '') }}"
                               placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                               class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm font-mono focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                        @error('twilio_sid') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="twilio_auth_token" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Auth Token</label>
                        <input type="password" name="twilio_auth_token" id="twilio_auth_token"
                               value="{{ old('twilio_auth_token', $maskedToken) }}"
                               placeholder="Votre token Twilio"
                               class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm font-mono focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                        <p class="mt-1 text-xs text-gray-400">Laissez vide pour conserver le token actuel</p>
                        @error('twilio_auth_token') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Numeros d'envoi</h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="twilio_whatsapp_from" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18a7.96 7.96 0 01-4.11-1.14l-.29-.174-3.01.79.81-2.95-.19-.3A7.96 7.96 0 014 12c0-4.418 3.582-8 8-8s8 3.582 8 8-3.582 8-8 8z"/></svg>
                                Numero WhatsApp
                            </span>
                        </label>
                        <input type="text" name="twilio_whatsapp_from" id="twilio_whatsapp_from"
                               value="{{ old('twilio_whatsapp_from', $settings['twilio_whatsapp_from'] ?? '') }}"
                               placeholder="whatsapp:+14155238886"
                               class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm font-mono focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                        @error('twilio_whatsapp_from') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="twilio_sms_from" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Numero SMS
                            </span>
                        </label>
                        <input type="text" name="twilio_sms_from" id="twilio_sms_from"
                               value="{{ old('twilio_sms_from', $settings['twilio_sms_from'] ?? '') }}"
                               placeholder="+1234567890"
                               class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm font-mono focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                        @error('twilio_sms_from') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TAB: Valeurs par defaut baux --}}
        {{-- ============================================================ --}}
        <div x-show="tab === 'baux'" x-cloak class="space-y-6">

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Valeurs par defaut pour les nouveaux baux</h3>
                <p class="text-xs text-gray-500 mb-6">Ces valeurs seront utilisees automatiquement lors de la creation d'un nouveau bail. Elles peuvent etre modifiees individuellement pour chaque bail.</p>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="default_penalty_rate" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Taux de penalite</label>
                        <div class="relative">
                            <input type="number" name="default_penalty_rate" id="default_penalty_rate" min="0" max="100" step="0.01"
                                   value="{{ old('default_penalty_rate', $settings['default_penalty_rate'] ?? '0') }}"
                                   class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2 pr-8">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 text-sm pointer-events-none">%</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Pourcentage applique sur le loyer en cas de retard</p>
                        @error('default_penalty_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="default_penalty_delay_days" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Delai de grace</label>
                        <div class="relative">
                            <input type="number" name="default_penalty_delay_days" id="default_penalty_delay_days" min="0" max="90"
                                   value="{{ old('default_penalty_delay_days', $settings['default_penalty_delay_days'] ?? '0') }}"
                                   class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2 pr-12">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 text-xs pointer-events-none">jours</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Nombre de jours avant application des penalites</p>
                        @error('default_penalty_delay_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="default_due_day" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Jour d'echeance</label>
                        <div class="relative">
                            <input type="number" name="default_due_day" id="default_due_day" min="1" max="28"
                                   value="{{ old('default_due_day', $settings['default_due_day'] ?? '5') }}"
                                   class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:border-brand-400 focus:ring-brand-400 transition py-2">
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Jour du mois ou le loyer est du (1 a 28)</p>
                        @error('default_due_day') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit button --}}
        <div class="flex justify-end pt-2">
            <button type="submit"
                    class="inline-flex items-center px-6 py-2.5 bg-brand-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 focus:bg-brand-700 active:bg-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Enregistrer les parametres
            </button>
        </div>
    </form>
</div>
@endsection
