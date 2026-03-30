@extends('layouts.app')

@section('title', 'Relances')

@section('actions')
    @can('create', App\Models\Reminder::class)
    <div class="flex items-center gap-1.5">
        <form method="POST" action="{{ route('reminders.auto-generate') }}" class="inline"
              x-data x-on:submit.prevent="if(confirm('Auto-g\u00e9n\u00e9rer les relances pour les \u00e9ch\u00e9ances impay\u00e9es ?')) $el.submit()">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 rounded-lg text-xs font-semibold text-white hover:bg-amber-700 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Auto-g&eacute;n&eacute;rer
            </button>
        </form>
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'new-reminder')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle relance
        </button>
    </div>
    @endcan
@endsection

@section('content')
    @php
        $statusBadgeTypes = [
            'brouillon' => 'en_attente',
            'envoye'    => 'success',
            'echec'     => 'danger',
        ];
        $statusLabels = [
            'brouillon' => 'Brouillon',
            'envoye'    => 'Envoyee',
            'echec'     => 'Echouee',
        ];
        $levelLabels = [
            1 => 'Courtois',
            2 => 'Ferme',
            3 => 'Mise en demeure',
        ];
        $levelBadgeClasses = [
            1 => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
            2 => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300',
            3 => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300',
        ];
        $channelLabels = [
            'email'    => 'Email',
            'sms'      => 'SMS',
            'whatsapp' => 'WhatsApp',
            'courrier' => 'Courrier',
        ];

    @endphp

    {{-- Stats cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {{-- En attente --}}
        <div class="bg-surface rounded-2xl border border-theme-subtle shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-accent-orange-50 dark:bg-accent-orange-950/30 text-accent-orange-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-on-surface-muted uppercase tracking-wider">En attente</p>
                <p class="text-xl font-bold text-on-surface">{{ $countBrouillon }}</p>
            </div>
        </div>
        {{-- Envoyees aujourd'hui --}}
        <div class="bg-surface rounded-2xl border border-theme-subtle shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-accent-green-50 dark:bg-accent-green-950/30 text-accent-green-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Envoy&eacute;es (aujourd'hui)</p>
                <p class="text-xl font-bold text-on-surface">{{ $countEnvoyeAujourdhui }}</p>
            </div>
        </div>
        {{-- Echecs --}}
        <div class="bg-surface rounded-2xl border border-theme-subtle shadow-sm px-5 py-4 flex items-center gap-4">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-accent-red-50 dark:bg-accent-red-950/30 text-accent-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-on-surface-muted uppercase tracking-wider">&Eacute;checs d'envoi</p>
                <p class="text-xl font-bold text-on-surface">{{ $countEchec }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <x-filters action="{{ route('reminders.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="status" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Statut</label>
                <select name="status" id="status" class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous</option>
                    <option value="brouillon" {{ request('status') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="envoye" {{ request('status') === 'envoye' ? 'selected' : '' }}>Envoy&eacute;e</option>
                    <option value="echec" {{ request('status') === 'echec' ? 'selected' : '' }}>&Eacute;chou&eacute;e</option>
                </select>
            </div>
            <div>
                <label for="channel" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Canal</label>
                <select name="channel" id="channel" class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous</option>
                    <option value="email" {{ request('channel') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="sms" {{ request('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="whatsapp" {{ request('channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="courrier" {{ request('channel') === 'courrier' ? 'selected' : '' }}>Courrier</option>
                </select>
            </div>
            <div>
                <label for="level" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Niveau</label>
                <select name="level" id="level" class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous</option>
                    <option value="1" {{ request('level') === '1' ? 'selected' : '' }}>Niveau 1 &mdash; Courtois</option>
                    <option value="2" {{ request('level') === '2' ? 'selected' : '' }}>Niveau 2 &mdash; Ferme</option>
                    <option value="3" {{ request('level') === '3' ? 'selected' : '' }}>Niveau 3 &mdash; Mise en demeure</option>
                </select>
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle shadow-sm overflow-hidden mt-6">
        @if($reminders->count())
            <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Date</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Locataire</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Bien</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Mois</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Canal</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Niveau</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Montant restant</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Statut</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme-subtle">
                        @foreach($reminders as $reminder)
                            <tr class="hover:bg-surface-hover/50 transition">
                                {{-- Date --}}
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-on-surface">
                                    {{ $reminder->created_at?->format('d/m/Y') }}
                                </td>

                                {{-- Locataire --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-md bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shrink-0">
                                            <span class="text-white font-semibold text-[10px]">{{ strtoupper(substr($reminder->leaseMonthly->lease->tenant->first_name ?? '', 0, 1) . substr($reminder->leaseMonthly->lease->tenant->last_name ?? '', 0, 1)) }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-on-surface">{{ $reminder->leaseMonthly->lease->tenant->full_name ?? '-' }}</span>
                                    </div>
                                </td>

                                {{-- Bien --}}
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">
                                    {{ $reminder->leaseMonthly->lease->property->reference ?? '-' }}
                                </td>

                                {{-- Mois --}}
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">
                                    {{ $reminder->leaseMonthly->month ?? '-' }}
                                </td>

                                {{-- Canal (icon) --}}
                                <td class="px-6 py-5 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        @switch($reminder->channel)
                                            @case('whatsapp')
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-500 text-white">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                </span>
                                                @break
                                            @case('sms')
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-500 text-white">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                </span>
                                                @break
                                            @case('email')
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-500 text-white">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                </span>
                                                @break
                                            @case('courrier')
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-500 text-white">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-300 text-white">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01"/>
                                                    </svg>
                                                </span>
                                        @endswitch
                                        <span class="text-on-surface-secondary">{{ $channelLabels[$reminder->channel] ?? ucfirst($reminder->channel) }}</span>
                                    </div>
                                </td>

                                {{-- Niveau (badge) --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($reminder->level)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $levelBadgeClasses[$reminder->level] ?? 'bg-surface-alt text-on-surface-secondary' }}">
                                            {{ $levelLabels[$reminder->level] ?? 'Niveau '.$reminder->level }}
                                        </span>
                                    @else
                                        <span class="text-sm text-on-surface-faint">&mdash;</span>
                                    @endif
                                </td>

                                {{-- Montant restant --}}
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-on-surface text-right">
                                    {{ number_format($reminder->leaseMonthly->remaining_amount ?? 0, 0, ',', ' ') }} FCFA
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <x-badge :type="$statusBadgeTypes[$reminder->status] ?? 'default'" :label="$statusLabels[$reminder->status] ?? $reminder->status" />
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm space-x-2">
                                    @if($reminder->status === 'brouillon')
                                        @can('create', App\Models\Reminder::class)
                                        <form method="POST" action="{{ route('reminders.send', $reminder) }}" class="inline"
                                              x-data x-on:submit.prevent="if(confirm('Envoyer cette relance ?')) $el.submit()">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-orange-600 bg-orange-50 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-800/30 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-800/40 dark:bg-orange-900/40 transition">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                </svg>
                                                Envoyer
                                            </button>
                                        </form>
                                        @endcan
                                    @endif
                                    <button type="button" data-open-modal="reminder-message-{{ $reminder->id }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-on-surface-secondary bg-surface-hover border border-theme rounded-lg hover:bg-surface-alt transition">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Voir
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-theme-subtle">
                {{ $reminders->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state message="Aucune relance ne correspond aux filtres s&eacute;lectionn&eacute;s." />
        @endif
    </div>

    {{-- Message modals for each reminder --}}
    @foreach($reminders as $reminder)
        <x-modal name="reminder-message-{{ $reminder->id }}" maxWidth="lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-on-surface mb-4">D&eacute;tail de la relance</h2>

                <div class="space-y-3 mb-5">
                    {{-- Canal --}}
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-medium text-on-surface-muted w-28">Canal</span>
                        <span class="text-on-surface">{{ $channelLabels[$reminder->channel] ?? ucfirst($reminder->channel) }}</span>
                    </div>
                    {{-- Niveau --}}
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-medium text-on-surface-muted w-28">Niveau</span>
                        @if($reminder->level)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $levelBadgeClasses[$reminder->level] ?? 'bg-surface-alt text-on-surface-secondary' }}">
                                {{ $levelLabels[$reminder->level] ?? 'Niveau '.$reminder->level }}
                            </span>
                        @else
                            <span class="text-on-surface-faint">&mdash;</span>
                        @endif
                    </div>
                    {{-- Envoyee le --}}
                    @if($reminder->sent_at)
                        <div class="flex items-center gap-2 text-sm">
                            <span class="font-medium text-on-surface-muted w-28">Envoy&eacute;e le</span>
                            <span class="text-on-surface">{{ $reminder->sent_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    {{-- Delivree le --}}
                    @if($reminder->delivered_at)
                        <div class="flex items-center gap-2 text-sm">
                            <span class="font-medium text-on-surface-muted w-28">D&eacute;livr&eacute;e le</span>
                            <span class="text-on-surface">{{ $reminder->delivered_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    {{-- Erreur --}}
                    @if($reminder->error_message)
                        <div class="flex items-start gap-2 text-sm">
                            <span class="font-medium text-on-surface-muted w-28 shrink-0">Erreur</span>
                            <span class="text-red-600">{{ $reminder->error_message }}</span>
                        </div>
                    @endif
                </div>

                <div class="bg-surface-hover/50 rounded-xl border border-theme-subtle p-4 text-sm text-on-surface-secondary whitespace-pre-line">{{ $reminder->message ?? 'Aucun message.' }}</div>

                <div class="mt-5 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close-modal', 'reminder-message-{{ $reminder->id }}')"
                            class="inline-flex items-center px-4 py-2 bg-surface border border-theme rounded-lg font-semibold text-xs text-on-surface-secondary uppercase tracking-widest hover:bg-surface-hover transition">
                        Fermer
                    </button>
                </div>
            </div>
        </x-modal>
    @endforeach

    {{-- New reminder modal (AJAX) --}}
    @can('create', App\Models\Reminder::class)
    <x-form-modal
        name="new-reminder"
        title="Nouvelle relance"
        :action="route('reminders.store')"
        method="POST"
        maxWidth="lg"
        submitLabel="Cr&eacute;er la relance"
        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>'
        iconColor="text-orange-500"
    >
        <div class="space-y-4">
            {{-- Echeance impayee --}}
            <div>
                <label for="new_lease_monthly_id" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">&Eacute;ch&eacute;ance impay&eacute;e <span class="text-red-500">*</span></label>
                <select name="lease_monthly_id" id="new_lease_monthly_id" required
                        class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">S&eacute;lectionner une &eacute;ch&eacute;ance</option>
                    @foreach($unpaidMonthlies as $monthly)
                        <option value="{{ $monthly->id }}">
                            {{ $monthly->month }} &mdash; {{ $monthly->lease->tenant->full_name ?? '?' }} &mdash; {{ $monthly->lease->property->reference ?? '?' }} ({{ number_format($monthly->remaining_amount, 0, ',', ' ') }} FCFA)
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Canal --}}
            <div>
                <label for="new_channel" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Canal <span class="text-red-500">*</span></label>
                <select name="channel" id="new_channel" required
                        class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">S&eacute;lectionner</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="sms">SMS</option>
                    <option value="email">Email</option>
                    <option value="courrier">Courrier</option>
                </select>
            </div>

            {{-- Niveau --}}
            <div>
                <label for="new_level" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Niveau <span class="text-red-500">*</span></label>
                <select name="level" id="new_level" required
                        class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">S&eacute;lectionner</option>
                    <option value="1">Niveau 1 &mdash; Courtois</option>
                    <option value="2">Niveau 2 &mdash; Ferme</option>
                    <option value="3">Niveau 3 &mdash; Mise en demeure</option>
                </select>
            </div>

            {{-- Message --}}
            <div>
                <label for="new_message" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Message</label>
                <textarea name="message" id="new_message" rows="5"
                          class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5"
                          placeholder="Message de relance..."></textarea>
            </div>
        </div>
    </x-form-modal>
    @endcan

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
@endpush
@endsection
