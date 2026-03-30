@extends('layouts.app')

@section('title', 'Documents')

@section('actions')
    @can('create', App\Models\Document::class)
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" type="button"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Générer un document
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" @click.outside="open = false" x-transition
             class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-surface ring-1 ring-black ring-opacity-5 z-10">
            <div class="py-1">
                <form method="POST" action="{{ route('documents.generate-quittance') }}" class="block">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-on-surface-secondary hover:bg-surface-alt">
                        Quittance de loyer
                    </button>
                </form>
                <form method="POST" action="{{ route('documents.generate-notice') }}" class="block">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-on-surface-secondary hover:bg-surface-alt">
                        Avis d'échéance
                    </button>
                </form>
                <form method="POST" action="{{ route('documents.generate-statement') }}" class="block">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-on-surface-secondary hover:bg-surface-alt">
                        Relevé de compte
                    </button>
                </form>
                <form method="POST" action="{{ route('documents.generate-monthly-report') }}" class="block">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-on-surface-secondary hover:bg-surface-alt">
                        Récapitulatif mensuel
                    </button>
                </form>
                <form method="POST" action="{{ route('documents.generate-attestation') }}" class="block">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-on-surface-secondary hover:bg-surface-alt">
                        Attestation de domicile
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan
@endsection

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('documents.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="type" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Type</label>
                <select name="type" id="type" class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous les types</option>
                    <option value="quittance" {{ request('type') === 'quittance' ? 'selected' : '' }}>Quittance</option>
                    <option value="recu" {{ request('type') === 'recu' ? 'selected' : '' }}>Reçu</option>
                    <option value="avis_echeance" {{ request('type') === 'avis_echeance' ? 'selected' : '' }}>Avis d'échéance</option>
                    <option value="releve" {{ request('type') === 'releve' ? 'selected' : '' }}>Relevé</option>
                    <option value="recap_mensuel" {{ request('type') === 'recap_mensuel' ? 'selected' : '' }}>Récap mensuel</option>
                    <option value="attestation" {{ request('type') === 'attestation' ? 'selected' : '' }}>Attestation</option>
                    <option value="bail_signe" {{ request('type') === 'bail_signe' ? 'selected' : '' }}>Bail signé</option>
                    <option value="etat_lieux" {{ request('type') === 'etat_lieux' ? 'selected' : '' }}>Etat des lieux</option>
                </select>
            </div>
            <div>
                <label for="month" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Mois</label>
                <input type="month" name="month" id="month" value="{{ request('month') }}"
                       class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle shadow-sm overflow-hidden mt-6">
        @if($documents->count())
            @php
                $typeLabels = [
                    'quittance' => 'Quittance',
                    'recu' => 'Reçu',
                    'avis_echeance' => 'Avis d\'échéance',
                    'releve' => 'Relevé',
                    'recap_mensuel' => 'Récap mensuel',
                    'attestation' => 'Attestation',
                    'bail_signe' => 'Bail signé',
                    'etat_lieux' => 'Etat des lieux',
                ];
            @endphp
            <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Date</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Type</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Entité liée</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Mois</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Généré par</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme-subtle">
                        @foreach($documents as $document)
                            <tr class="hover:bg-surface-hover/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-accent-orange-400 to-accent-orange-500 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <span class="text-sm font-medium text-on-surface">{{ $document->created_at?->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <x-badge type="en_attente" :label="$typeLabels[$document->type] ?? ucfirst(str_replace('_', ' ', $document->type))" />
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">
                                    @if($document->related)
                                        {{ class_basename($document->related_type) }} #{{ $document->related_id }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $document->month ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $document->generator->name ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm space-x-2">
                                    <a href="{{ route('documents.show', $document) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-on-surface-secondary bg-surface-hover border border-theme rounded-lg hover:bg-surface-alt transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Voir</a>
                                    <a href="{{ route('documents.download', $document) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-accent-green-500 bg-accent-green-50 dark:bg-accent-green-950/30 border border-accent-green-200 dark:border-accent-green-800/30 rounded-lg hover:bg-accent-green-100 dark:hover:bg-accent-green-900/40 transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>Télécharger</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-theme-subtle">
                {{ $documents->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state message="Aucun document ne correspond aux filtres sélectionnés." />
        @endif
    </div>

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
@endpush
@endsection
