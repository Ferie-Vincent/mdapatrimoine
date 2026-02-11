@extends('layouts.app')

@section('title', 'Log #' . $auditLog->id)

@section('actions')
    <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm print:hidden">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Imprimer
    </button>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('audit-logs.index') }}" class="text-sm text-brand-600 hover:text-brand-900">&larr; Retour au journal</a>
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
            'created' => 'bg-green-100 text-green-800',
            'updated' => 'bg-blue-100 text-blue-800',
            'deleted' => 'bg-red-100 text-red-800',
            'login' => 'bg-purple-100 text-purple-800',
            'generated_document' => 'bg-brand-100 text-brand-800',
            'recorded_payment' => 'bg-emerald-100 text-emerald-800',
        ];
    @endphp

    {{-- Detail card --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Détails de l'entrée</h3>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $actionColors[$auditLog->action] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $actionLabels[$auditLog->action] ?? $auditLog->action }}
            </span>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Utilisateur</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->user->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">SCI</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->sci->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Action</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $actionLabels[$auditLog->action] ?? $auditLog->action }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Type d'entité</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($auditLog->entity_type)
                            {{ class_basename($auditLog->entity_type) }}
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">ID de l'entité</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->entity_id ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Adresse IP</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-900">{{ $auditLog->ip_address ?? '-' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                    <dd class="mt-1 text-sm text-gray-500 break-all">{{ $auditLog->user_agent ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date / Heure</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at?->format('d/m/Y H:i:s') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Changes --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Modifications</h3>
        </div>
        <div class="p-6">
            @if($auditLog->changes && count($auditLog->changes))
                <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-gray-700 whitespace-pre-wrap font-mono">{{ json_encode($auditLog->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @else
                <p class="text-sm text-gray-500">Aucune modification enregistrée.</p>
            @endif
        </div>
    </div>
@endsection
