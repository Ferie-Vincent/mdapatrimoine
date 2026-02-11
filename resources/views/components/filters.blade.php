@props([
    'action' => null,
    'method' => 'GET',
])

@php
    $activeCount = collect(request()->except('page'))->filter(fn($v) => $v !== null && $v !== '')->count();
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </div>
            <div class="flex-1 flex items-center gap-2.5">
                <span class="text-sm font-bold text-gray-800">Filtres</span>
                @if($activeCount > 0)
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-brand-500 text-white text-[10px] font-bold leading-none shadow-sm">{{ $activeCount }}</span>
                @endif
            </div>
            @if($activeCount > 0)
                <a href="{{ $action ?? request()->url() }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-400 hover:text-accent-red-500 hover:bg-accent-red-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Effacer
                </a>
            @endif
        </div>

        {{-- Form --}}
        <form method="{{ $method }}" action="{{ $action ?? request()->url() }}" x-ref="filterForm"
              x-on:change="if ($event.target.tagName === 'SELECT' || $event.target.type === 'date' || $event.target.type === 'month') $refs.filterForm.submit()"
              x-on:input.debounce.400ms="if ($event.target.tagName === 'INPUT' && ($event.target.type === 'text' || $event.target.type === 'number' || $event.target.type === 'search')) $refs.filterForm.submit()">
            {{ $slot }}
        </form>
    </div>
</div>
