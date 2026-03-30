@extends('layouts.app')

@section('title', 'Log #' . $auditLog->id)

@section('actions')
    <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface border border-theme rounded-lg text-xs font-medium text-on-surface-secondary hover:bg-surface-hover hover:border-theme transition shadow-sm print:hidden">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Imprimer
    </button>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('audit-logs.index') }}" class="text-sm text-brand-600 dark:text-brand-400 hover:text-brand-900 dark:hover:text-brand-300">&larr; Retour au journal</a>
    </div>

    @php
        $actionLabels = [
            'created' => 'Création',
            'updated' => 'Modification',
            'deleted' => 'Suppression',
            'login' => 'Connexion',
            'generated_document' => 'Document généré',
            'recorded_payment' => 'Paiement enregistré',
        ];
        $actionColors = [
            'created' => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200',
            'updated' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200',
            'deleted' => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200',
            'login' => 'bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-200',
            'generated_document' => 'bg-brand-100 dark:bg-brand-900/40 text-brand-800',
            'recorded_payment' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-200',
        ];
    @endphp

    {{-- Detail card --}}
    <div class="bg-surface rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-theme flex items-center justify-between">
            <h3 class="text-lg font-medium text-on-surface">Détails de l'entrée</h3>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $actionColors[$auditLog->action] ?? 'bg-surface-alt text-on-surface' }}">
                {{ $actionLabels[$auditLog->action] ?? $auditLog->action }}
            </span>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm font-medium text-on-surface-muted">Utilisateur</dt>
                    <dd class="mt-1 text-sm text-on-surface">{{ $auditLog->user->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-on-surface-muted">SCI</dt>
                    <dd class="mt-1 text-sm text-on-surface">{{ $auditLog->sci->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-on-surface-muted">Action</dt>
                    <dd class="mt-1 text-sm text-on-surface">{{ $actionLabels[$auditLog->action] ?? $auditLog->action }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-on-surface-muted">Type d'entité</dt>
                    <dd class="mt-1 text-sm text-on-surface">
                        @if($auditLog->entity_type)
                            {{ class_basename($auditLog->entity_type) }}
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-on-surface-muted">ID de l'entité</dt>
                    <dd class="mt-1 text-sm text-on-surface">{{ $auditLog->entity_id ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-on-surface-muted">Adresse IP</dt>
                    <dd class="mt-1 text-sm font-mono text-on-surface">{{ $auditLog->ip_address ?? '-' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-on-surface-muted">User Agent</dt>
                    <dd class="mt-1 text-sm text-on-surface-muted break-all">{{ $auditLog->user_agent ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-on-surface-muted">Date / Heure</dt>
                    <dd class="mt-1 text-sm text-on-surface">{{ $auditLog->created_at?->format('d/m/Y H:i:s') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Changes --}}
    <div class="bg-surface rounded-lg shadow">
        <div class="px-6 py-4 border-b border-theme">
            <h3 class="text-lg font-medium text-on-surface">Modifications</h3>
        </div>
        <div class="p-6">
            @if($auditLog->changes && count($auditLog->changes))
                <div class="bg-surface-hover rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-on-surface-secondary whitespace-pre-wrap font-mono">{{ json_encode($auditLog->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @else
                <p class="text-sm text-on-surface-muted">Aucune modification enregistrée.</p>
            @endif
        </div>
    </div>
@endsection
