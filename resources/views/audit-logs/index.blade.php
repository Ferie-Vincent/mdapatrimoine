@extends('layouts.app')

@section('title', "Journal d'activité")

@section('content')
    {{-- Filters --}}
    <x-filters action="{{ route('audit-logs.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="user_id" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Utilisateur</label>
                <select name="user_id" id="user_id" class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Tous</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="action" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Action</label>
                <select name="action" id="action" class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
                    <option value="">Toutes</option>
                    <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Création</option>
                    <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Modification</option>
                    <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Suppression</option>
                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Connexion</option>
                    <option value="generated_document" {{ request('action') === 'generated_document' ? 'selected' : '' }}>Document généré</option>
                    <option value="recorded_payment" {{ request('action') === 'recorded_payment' ? 'selected' : '' }}>Paiement enregistré</option>
                </select>
            </div>
            <div>
                <label for="entity_type" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Entité</label>
                <div class="relative">
                    <input type="text" name="entity_type" id="entity_type" value="{{ request('entity_type') }}" placeholder="Type d'entité..."
                           class="block w-full py-2.5 rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface placeholder-on-surface-faint hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all">
                </div>
            </div>
            <div>
                <label for="date_from" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Date début</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                       class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
            </div>
            <div>
                <label for="date_to" class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Date fin</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                       class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface hover:border-brand-200 dark:border-gray-600 focus:bg-surface focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition-all py-2.5">
            </div>
        </div>
    </x-filters>

    {{-- Table --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle shadow-sm overflow-hidden mt-6">
        @if($auditLogs->count())
            @php
                $actionBadgeTypes = [
                    'created' => 'success',
                    'updated' => 'en_attente',
                    'deleted' => 'danger',
                    'generated_document' => 'en_attente',
                    'recorded_payment' => 'success',
                ];
                $actionLabels = [
                    'created' => 'Création',
                    'updated' => 'Modification',
                    'deleted' => 'Suppression',
                    'login' => 'Connexion',
                    'generated_document' => 'Document généré',
                    'recorded_payment' => 'Paiement',
                ];
            @endphp
            <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Date / Heure</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Utilisateur</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">SCI</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Action</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Entité</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">Détails</th>
                            <th class="px-6 py-3.5 text-left text-sm font-medium text-on-surface-faint">IP</th>
                            <th class="px-6 py-3.5 text-right text-sm font-medium text-on-surface-faint">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme-subtle">
                        @foreach($auditLogs as $log)
                            <tr class="hover:bg-surface-hover/50 transition">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-md bg-brand-50 dark:bg-brand-950/30 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <span class="text-sm font-medium text-on-surface">{{ $log->created_at?->format('d/m/Y H:i:s') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-md bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shrink-0">
                                            <span class="text-white font-bold text-[9px]">{{ strtoupper(substr($log->user->name ?? '-', 0, 2)) }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-on-surface">{{ $log->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">{{ $log->sci->name ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($log->action === 'login')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium ring-1 ring-inset bg-brand-50 dark:bg-brand-950/30 text-brand-600 dark:text-brand-400 ring-brand-500/20">
                                            {{ $actionLabels[$log->action] ?? $log->action }}
                                        </span>
                                    @else
                                        <x-badge :type="$actionBadgeTypes[$log->action] ?? 'default'" :label="$actionLabels[$log->action] ?? $log->action" />
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-secondary">
                                    @if($log->entity_type)
                                        {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-sm text-on-surface-secondary max-w-xs">
                                    @if($log->changes)
                                        @php
                                            $c = $log->changes;
                                            $summary = [];

                                            // Payment
                                            if (isset($c['amount'])) {
                                                $summary[] = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 text-xs font-medium">' . number_format((float)$c['amount'], 0, ',', ' ') . ' F</span>';
                                            }
                                            if (isset($c['method'])) {
                                                $methods = ['especes' => 'Espèces', 'virement' => 'Virement', 'cheque' => 'Chèque', 'mobile_money' => 'Mobile Money', 'depot_bancaire' => 'Dépôt bancaire'];
                                                $summary[] = '<span class="text-xs text-on-surface-muted">' . ($methods[$c['method']] ?? $c['method']) . '</span>';
                                            }

                                            // Document
                                            if (isset($c['type']) && isset($c['filename'])) {
                                                $docTypes = ['quittance' => 'Quittance', 'recu_entree_agence' => 'Reçu agence', 'recu_entree_caution' => 'Reçu caution', 'avis_echeance' => 'Avis échéance'];
                                                $summary[] = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 text-xs font-medium">' . ($docTypes[$c['type']] ?? $c['type']) . '</span>';
                                            }

                                            // Lease / Entity creation
                                            if (isset($c['status'])) {
                                                $statuses = ['actif' => ['Actif', 'emerald'], 'resilie' => ['Résilié', 'red'], 'en_attente' => ['En attente', 'amber']];
                                                $s = $statuses[$c['status']] ?? [ucfirst($c['status']), 'gray'];
                                                $summary[] = '<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-' . $s[1] . '-50 dark:bg-' . $s[1] . '-950/30 text-' . $s[1] . '-700 dark:text-' . $s[1] . '-400 text-xs font-medium">' . $s[0] . '</span>';
                                            }

                                            // Property
                                            if (isset($c['city'])) {
                                                $summary[] = '<span class="text-xs text-on-surface-muted">' . e($c['city']) . '</span>';
                                            }
                                            if (isset($c['type']) && !isset($c['filename'])) {
                                                $types = ['appartement' => 'Appt', 'maison' => 'Maison', 'bureau' => 'Bureau', 'commerce' => 'Commerce', 'studio' => 'Studio'];
                                                $summary[] = '<span class="text-xs text-on-surface-muted">' . ($types[$c['type']] ?? ucfirst($c['type'])) . '</span>';
                                            }
                                            if (isset($c['rooms'])) {
                                                $summary[] = '<span class="text-xs text-on-surface-muted">' . $c['rooms'] . ' pce(s)</span>';
                                            }
                                            if (isset($c['rent_amount'])) {
                                                $summary[] = '<span class="text-xs font-medium text-on-surface-muted">' . number_format((float)$c['rent_amount'], 0, ',', ' ') . ' F/mois</span>';
                                            }
                                        @endphp

                                        @if(count($summary))
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                {!! implode('', $summary) !!}
                                            </div>
                                        @else
                                            <span class="text-on-surface-faint text-xs italic">{{ Str::limit(collect($c)->map(fn($v, $k) => $k . ': ' . (is_string($v) ? $v : json_encode($v)))->implode(', '), 60) }}</span>
                                        @endif
                                    @else
                                        <span class="text-on-surface-faint">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-on-surface-faint font-mono">{{ $log->ip_address ?? '-' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('audit-logs.show', $log) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-on-surface-secondary bg-surface-hover border border-theme rounded-lg hover:bg-surface-alt transition"><svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Détails</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-theme-subtle">
                {{ $auditLogs->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state message="Aucune entrée de journal ne correspond aux filtres sélectionnés." />
        @endif
    </div>

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => SCIDataTable('#dataTable'));</script>
@endpush
@endsection
