@extends('layouts.app')

@section('title', 'Document #' . $document->id)

@section('actions')
    <div class="flex items-center gap-1.5">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimer
        </button>
        <a href="{{ route('documents.download', $document) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 rounded-lg text-xs font-semibold text-white hover:bg-brand-700 transition shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Télécharger
        </a>
    </div>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('documents.index') }}" class="text-sm text-brand-600 hover:text-brand-900">&larr; Retour aux documents</a>
    </div>

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

    {{-- Detail card --}}
    <div class="bg-white rounded-2xl border border-gray-100 mb-6 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-slate-800 to-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Document #{{ $document->id }}</h3>
                    <p class="text-sm text-slate-300">{{ $document->created_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ring-1 bg-blue-400/20 text-blue-300 ring-blue-400/30">
                {{ $typeLabels[$document->type] ?? ucfirst(str_replace('_', ' ', $document->type)) }}
            </span>
        </div>

        <div class="px-6 py-5 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Informations</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Type</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $typeLabels[$document->type] ?? ucfirst(str_replace('_', ' ', $document->type)) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Entite liee</p>
                        <p class="text-sm font-semibold text-gray-900">
                            @if($document->related)
                                {{ class_basename($document->related_type) }} #{{ $document->related_id }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Mois</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $document->month ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Genere par</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $document->generator->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Date de creation</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $document->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">SCI</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $document->sci->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PDF Preview --}}
    @if($document->path && str_ends_with($document->path, '.pdf'))
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Apercu</h3>
            </div>
            <div class="p-6">
                <iframe src="{{ route('documents.preview', $document) }}"
                        class="w-full rounded-lg border border-gray-200"
                        style="height: 800px;"
                        title="Apercu du document">
                </iframe>
            </div>
        </div>
    @endif
@endsection
