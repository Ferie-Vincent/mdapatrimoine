@extends('layouts.app')

@section('title', 'Ma Journee')

@section('content')
<div x-data="kanbanBoard({
    columns: {{ Js::from($kanbanData) }},
    selectedDate: '{{ $selectedDate }}',
    showArchived: {{ $showArchived ? 'true' : 'false' }},
    isSuperAdmin: {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }},
    canCreate: {{ auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire() ? 'true' : 'false' }},
    assignableUsers: {{ Js::from($assignableUsers) }},
    routes: {
        index: '{{ route('tasks.index') }}',
        store: '{{ route('tasks.store') }}',
        move: '{{ url('/tasks') }}',
        reorder: '{{ route('tasks.reorder') }}',
        generateAuto: '{{ route('tasks.generate-auto') }}',
    },
    csrfToken: '{{ csrf_token() }}'
})">

    {{-- Progress banner --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle shadow-sm p-4 sm:p-5 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-on-surface">
                        Aujourd'hui : <span x-text="completedCount"></span>/<span x-text="totalCount"></span> taches completees
                    </h2>
                    <p class="text-xs text-on-surface-faint mt-0.5" x-text="selectedDate === todayStr ? 'Aujourd\'hui' : selectedDate"></p>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                {{-- Date picker --}}
                <input type="date" x-model="selectedDate" @change="refreshBoard()"
                       class="text-sm rounded-lg border-theme bg-surface-hover text-on-surface py-2 px-3 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 transition">

                {{-- Recap eye button --}}
                <button @click="recapOpen = true"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-theme bg-surface-hover text-on-surface-secondary hover:text-brand-500 hover:border-brand-400 transition relative"
                        title="Recap des taches">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span x-show="pendingTasks.length > 0"
                          class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center"
                          x-text="pendingTasks.length"></span>
                </button>

                {{-- Generate auto tasks --}}
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire())
                <button @click="generateAutoTasks()" :disabled="generating"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm transition disabled:opacity-50">
                    <svg class="w-4 h-4" :class="{ 'animate-spin': generating }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!generating" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        <path x-show="generating" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-text="generating ? 'Generation...' : 'Generer les taches du jour'"></span>
                </button>
                @endif

                {{-- Toggle archived --}}
                <label class="inline-flex items-center gap-2 text-sm text-on-surface-secondary cursor-pointer">
                    <input type="checkbox" x-model="showArchived" @change="refreshBoard()"
                           class="rounded border-theme text-brand-600 focus:ring-brand-500/20">
                    Archivees
                </label>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mt-3 h-2 bg-surface-alt rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-green-400 to-green-500 rounded-full transition-all duration-500"
                 :style="'width: ' + progressPercent + '%'"></div>
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory pb-4 -mx-4 px-4 lg:mx-0 lg:px-0"
         style="height: calc(100vh - 16rem);">

        @php
            $columnConfig = [
                'a_faire' => [
                    'label' => 'A faire',
                    'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                    'iconBg' => 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400',
                    'badgeBg' => 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400',
                    'headerBg' => '',
                ],
                'en_cours' => [
                    'label' => 'En cours',
                    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                    'iconBg' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                    'badgeBg' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                    'headerBg' => '',
                ],
                'bloque' => [
                    'label' => 'Bloque',
                    'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.072 16.5c-.77.833.192 2.5 1.732 2.5z',
                    'iconBg' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
                    'badgeBg' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
                    'headerBg' => 'bg-amber-50/50 dark:bg-amber-950/20',
                ],
                'fait' => [
                    'label' => 'Fait',
                    'icon' => 'M5 13l4 4L19 7',
                    'iconBg' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                    'badgeBg' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                    'headerBg' => '',
                ],
                'archive' => [
                    'label' => 'Archive',
                    'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
                    'iconBg' => 'bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400',
                    'badgeBg' => 'bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400',
                    'headerBg' => '',
                ],
            ];
        @endphp

        @foreach($columnConfig as $status => $config)
        <div class="snap-start shrink-0 w-[300px] sm:w-[320px] flex flex-col bg-surface rounded-2xl border border-theme-subtle shadow-sm"
             x-show="'{{ $status }}' !== 'archive' || showArchived">

            {{-- Column header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-theme-subtle shrink-0 {{ $config['headerBg'] }}">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ $config['iconBg'] }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-on-surface">{{ $config['label'] }}</h3>
                </div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $config['badgeBg'] }}"
                      x-text="(columns['{{ $status }}'] || []).length"></span>
            </div>

            {{-- Cards container (sortable) --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-3"
                 x-ref="column_{{ $status }}"
                 data-status="{{ $status }}">

                <template x-for="task in columns['{{ $status }}'] || []" :key="task.id">
                    <div class="bg-surface border border-theme-subtle rounded-xl p-3.5 cursor-grab active:cursor-grabbing shadow-sm hover:shadow-md transition-shadow group"
                         :class="{ 'border-l-4 border-l-red-500': task.is_overdue }"
                         :data-id="task.id">

                        {{-- Line 1: Category badge + priority --}}
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium"
                                  :class="categoryClass(task.category)"
                                  x-text="categoryLabel(task.category)"></span>
                            <div class="flex items-center gap-1.5">
                                <template x-if="task.priority === 'haute'">
                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 4.414l-3.293 3.293a1 1 0 01-1.414 0zm0 6a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 10.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                </template>
                                <template x-if="task.priority === 'basse'">
                                    <svg class="w-4 h-4 text-on-surface-faint" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 15.586l3.293-3.293a1 1 0 011.414 0zm0-6a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L10 9.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </template>
                                {{-- Actions on hover --}}
                                <div class="opacity-0 group-hover:opacity-100 flex items-center gap-0.5 transition">
                                    <template x-if="canCreate">
                                        <button @click.stop="editTask(task)" class="p-1 rounded text-on-surface-faint hover:text-brand-500 hover:bg-surface-hover transition" title="Modifier">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </template>
                                    <template x-if="canCreate">
                                        <button @click.stop="deleteTask(task.id)" class="p-1 rounded text-on-surface-faint hover:text-red-500 hover:bg-surface-hover transition" title="Supprimer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Line 2: Title --}}
                        <h4 class="text-sm font-semibold text-on-surface leading-snug mb-1.5" x-text="task.title"></h4>

                        {{-- Line 3: Amount + related entity --}}
                        <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                            <template x-if="task.amount && parseFloat(task.amount) > 0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400"
                                      x-text="formatAmount(task.amount)"></span>
                            </template>
                            <template x-if="task.related">
                                <span class="text-xs text-brand-600 dark:text-brand-400" x-text="relatedLabel(task)"></span>
                            </template>
                            <template x-if="task.is_recurring">
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span x-text="recurrenceLabel(task.recurrence)"></span>
                                </span>
                            </template>
                        </div>

                        {{-- Line 4: SCI + time + assignee --}}
                        <div class="flex items-center gap-2 text-[11px] text-on-surface-faint">
                            <template x-if="task.sci">
                                <span x-text="task.sci.name"></span>
                            </template>
                            <template x-if="task.scheduled_time">
                                <span class="flex items-center gap-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span x-text="task.scheduled_time?.substring(0, 5)"></span>
                                </span>
                            </template>
                            <template x-if="isSuperAdmin && task.user">
                                <span class="ml-auto flex items-center gap-1">
                                    <div class="w-4 h-4 rounded-full bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center">
                                        <span class="text-[8px] font-bold text-brand-600 dark:text-brand-400" x-text="task.user.name?.substring(0, 1).toUpperCase()"></span>
                                    </div>
                                    <span x-text="task.user.name"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @endforeach
    </div>

    {{-- FAB mobile (create task) --}}
    @if(auth()->user()->isSuperAdmin() || auth()->user()->isGestionnaire())
    <button @click="openCreateModal()"
            class="fixed bottom-20 right-4 lg:bottom-6 lg:right-6 z-30 w-14 h-14 rounded-full bg-accent-orange-500 hover:bg-accent-orange-600 text-white shadow-xl flex items-center justify-center transition-transform hover:scale-105 active:scale-95 print:hidden">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
    </button>
    @endif

    {{-- Recap Modal --}}
    <div x-show="recapOpen" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50" style="display:none;">
        <div @click.away="recapOpen = false" x-transition
             class="bg-surface rounded-2xl border border-theme-subtle shadow-2xl w-full max-w-lg max-h-[85vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-theme-subtle">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-on-surface">Recap des taches</h3>
                </div>
                <button @click="recapOpen = false" class="p-1 rounded-lg text-on-surface-faint hover:text-on-surface hover:bg-surface-hover transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                {{-- Summary stats --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-brand-50 dark:bg-brand-950/30 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-brand-600 dark:text-brand-400" x-text="(columns.a_faire || []).length"></div>
                        <div class="text-[11px] font-medium text-brand-600/70 dark:text-brand-400/70 uppercase">A faire</div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-950/30 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="(columns.en_cours || []).length"></div>
                        <div class="text-[11px] font-medium text-blue-600/70 dark:text-blue-400/70 uppercase">En cours</div>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-950/30 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400" x-text="(columns.bloque || []).length"></div>
                        <div class="text-[11px] font-medium text-amber-600/70 dark:text-amber-400/70 uppercase">Bloque</div>
                    </div>
                </div>

                {{-- Total amount --}}
                <template x-if="recapTotalAmount > 0">
                    <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-950/30 rounded-xl px-4 py-3">
                        <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Montant total a encaisser</span>
                        <span class="text-lg font-bold text-emerald-700 dark:text-emerald-400" x-text="formatAmount(recapTotalAmount)"></span>
                    </div>
                </template>

                {{-- Tasks by category --}}
                <template x-for="[cat, group] in Object.entries(recapByCategory)" :key="cat">
                    <div class="border border-theme-subtle rounded-xl overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 bg-surface-hover/50">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium"
                                      :class="categoryClass(cat)"
                                      x-text="categoryLabel(cat)"></span>
                                <span class="text-xs text-on-surface-faint" x-text="group.tasks.length + ' tache(s)'"></span>
                            </div>
                            <template x-if="group.total > 0">
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400" x-text="formatAmount(group.total)"></span>
                            </template>
                        </div>
                        <div class="divide-y divide-theme-subtle">
                            <template x-for="task in group.tasks" :key="task.id">
                                <div class="flex items-center gap-3 px-4 py-2.5">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-on-surface truncate" x-text="task.title"></div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <template x-if="task.sci">
                                                <span class="text-[11px] text-on-surface-faint" x-text="task.sci.name"></span>
                                            </template>
                                            <template x-if="task.is_recurring">
                                                <span class="inline-flex items-center gap-0.5 text-[10px] text-indigo-500">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span x-text="recurrenceLabel(task.recurrence)"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <template x-if="task.amount && parseFloat(task.amount) > 0">
                                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400" x-text="formatAmount(task.amount)"></span>
                                        </template>
                                        <template x-if="task.priority === 'haute'">
                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 4.414l-3.293 3.293a1 1 0 01-1.414 0zm0 6a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 10.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Empty state --}}
                <template x-if="pendingTasks.length === 0">
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-on-surface-secondary">Toutes les taches sont completees !</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div x-show="modalOpen" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50" style="display:none;">
        <div @click.away="modalOpen = false" x-transition
             class="bg-surface rounded-2xl border border-theme-subtle shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-theme-subtle">
                <h3 class="text-lg font-bold text-on-surface" x-text="editingTask ? 'Modifier la tache' : 'Nouvelle tache'"></h3>
                <button @click="modalOpen = false" class="p-1 rounded-lg text-on-surface-faint hover:text-on-surface hover:bg-surface-hover transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="submitTask()" class="p-6 space-y-4">
                {{-- Title --}}
                <div>
                    <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Titre *</label>
                    <input type="text" x-model="form.title" required
                           class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition"
                           placeholder="Ex: Encaisser loyer M. Diallo">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Description</label>
                    <textarea x-model="form.description" rows="2"
                              class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition"
                              placeholder="Details optionnels..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Category --}}
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Categorie *</label>
                        <select x-model="form.category" required
                                class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition">
                            <option value="encaissement">Encaissement</option>
                            <option value="relance">Relance</option>
                            <option value="visite">Visite</option>
                            <option value="administratif">Administratif</option>
                            <option value="document">Document</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Priorite *</label>
                        <select x-model="form.priority" required
                                class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition">
                            <option value="haute">Haute</option>
                            <option value="moyenne">Moyenne</option>
                            <option value="basse">Basse</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Date --}}
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Date *</label>
                        <input type="date" x-model="form.scheduled_date" required
                               class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition">
                    </div>

                    {{-- Time --}}
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Heure</label>
                        <input type="time" x-model="form.scheduled_time"
                               class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition">
                    </div>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Montant (FCFA)</label>
                    <input type="number" x-model="form.amount" min="0" step="1"
                           class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition"
                           placeholder="0">
                </div>

                {{-- Reminder --}}
                <div>
                    <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Rappel push</label>
                    <input type="datetime-local" x-model="form.reminder_at"
                           class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition">
                </div>

                {{-- Recurrence --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Repetition</label>
                        <select x-model="form.recurrence"
                                class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition">
                            <option value="">Aucune</option>
                            <option value="quotidien">Quotidien</option>
                            <option value="hebdomadaire">Hebdomadaire</option>
                            <option value="mensuel">Mensuel</option>
                            <option value="trimestriel">Trimestriel</option>
                            <option value="annuel">Annuel</option>
                        </select>
                    </div>
                    <div x-show="form.recurrence" x-transition>
                        <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Jusqu'au</label>
                        <input type="date" x-model="form.recurrence_end_date"
                               class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition"
                               placeholder="Indefini">
                    </div>
                </div>

                {{-- Assign (super_admin only) --}}
                <template x-if="isSuperAdmin && Object.keys(assignableUsers).length > 0">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted/80 uppercase tracking-wider mb-1">Assigner a</label>
                        <select x-model="form.user_id"
                                class="block w-full rounded-xl border-theme bg-surface-hover/70 text-sm text-on-surface py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20 transition">
                            <option value="">Moi-meme</option>
                            <template x-for="[id, name] in Object.entries(assignableUsers)" :key="id">
                                <option :value="id" x-text="name"></option>
                            </template>
                        </select>
                    </div>
                </template>

                {{-- Submit --}}
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 text-sm font-medium text-on-surface-secondary bg-surface-hover border border-theme rounded-lg hover:bg-surface-alt transition">
                        Annuler
                    </button>
                    <button type="submit" :disabled="submitting"
                            class="px-5 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm transition disabled:opacity-50">
                        <span x-text="submitting ? 'Enregistrement...' : (editingTask ? 'Modifier' : 'Creer')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
