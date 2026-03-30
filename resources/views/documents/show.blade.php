@extends('layouts.app')

@section('title', 'Document #' . $document->id)

@section('content')
    @php
        $typeLabels = [
            'quittance' => 'Quittance', 'recu' => 'Reçu', 'avis_echeance' => 'Avis d\'échéance',
            'releve' => 'Relevé', 'recap_mensuel' => 'Récap mensuel', 'attestation' => 'Attestation',
            'bail_signe' => 'Bail signé', 'etat_lieux' => 'Etat des lieux',
        ];
    @endphp

    {{-- Hero Header --}}
    <div class="relative bg-gradient-to-br from-brand-700 via-brand-500 to-indigo-500 dark:from-brand-900 dark:via-brand-700 dark:to-indigo-800 rounded-2xl overflow-hidden mb-6 print:hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 44px 44px;"></div>
        <div class="relative flex items-center justify-between px-5 pt-5">
            <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center p-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
                <a href="{{ route('documents.download', $document) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg text-sm font-medium text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Télécharger
                </a>
            </div>
        </div>
        <div class="relative text-center px-6 pb-8 pt-6">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-sm text-white border border-white/20">
                {{ $typeLabels[$document->type] ?? ucfirst(str_replace('_', ' ', $document->type)) }}
            </span>
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-3">Document #{{ $document->id }}</h1>
            <p class="text-white/70 mt-2">{{ $document->created_at?->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    {{-- Detail card --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle mb-6 overflow-hidden">
        <div class="px-6 py-5 border-b border-theme-subtle">
            <p class="text-xs font-semibold text-on-surface-faint uppercase tracking-wider mb-4">Informations</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Type</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $typeLabels[$document->type] ?? ucfirst(str_replace('_', ' ', $document->type)) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Entite liee</p>
                        <p class="text-sm font-semibold text-on-surface">
                            @if($document->related)
                                {{ class_basename($document->related_type) }} #{{ $document->related_id }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Mois</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $document->month ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Genere par</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $document->generator->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-alt flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-on-surface-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">Date de creation</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $document->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-faint">SCI</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $document->sci->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PDF Preview --}}
    @if($document->path && str_ends_with($document->path, '.pdf'))
        <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden">
            <div class="px-6 py-4 border-b border-theme-subtle flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-on-surface">Apercu</h3>
            </div>
            <div class="p-6">
                <iframe src="{{ route('documents.preview', $document) }}"
                        class="w-full rounded-lg border border-theme"
                        style="height: 800px;"
                        title="Apercu du document">
                </iframe>
            </div>
        </div>
    @endif
@endsection
