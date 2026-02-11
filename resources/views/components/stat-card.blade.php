@props([
    'title'      => '',
    'value'      => '',
    'subtitle'   => null,
    'color'      => 'blue',
    'trend'      => null,
    'trendValue' => null,
    'icon'       => null,
])

@php
$trendColor = match($trend ?? '') {
    'up'   => 'text-accent-green-400 bg-accent-green-50',
    'down' => 'text-accent-red-400 bg-accent-red-50',
    default => 'text-gray-400 bg-gray-50',
};

$iconBg = match($color) {
    'green'  => 'from-accent-green-400 to-accent-green-500',
    'red'    => 'from-accent-red-400 to-accent-red-500',
    'orange' => 'from-accent-orange-400 to-accent-orange-500',
    'yellow' => 'from-accent-yellow-300 to-accent-yellow-400',
    'purple' => 'from-accent-orange-400 to-accent-red-400',
    default  => 'from-brand-400 to-brand-600',
};

$blobColor = match($color) {
    'green'  => '#1E9B3E',
    'red'    => '#C42618',
    'orange' => '#D4812A',
    'yellow' => '#F0D020',
    'purple' => '#8B5CF6',
    default  => '#1E3A8A',
};

$defaultIcons = [
    'blue'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'green'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'red'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>',
    'orange' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
    'yellow' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
    'purple' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
];

$iconSvg = $icon ?? ($defaultIcons[$color] ?? $defaultIcons['blue']);
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow duration-200 relative overflow-hidden']) }}>
    {{-- Decorative colored blob top-right --}}
    <div class="absolute -top-5 -right-5 w-20 h-20 rounded-full pointer-events-none" style="background: {{ $blobColor }}; opacity: 0.08;"></div>

    <div class="relative flex items-start gap-4">
        {{-- Icon --}}
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $iconBg }} flex items-center justify-center shrink-0 shadow-sm">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $iconSvg !!}
            </svg>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm text-gray-500 mb-1">{{ $title }}</p>
            <div class="flex items-end justify-between gap-2">
                <p class="text-xl font-bold text-gray-900 tracking-tight truncate">{{ $value }}</p>
                @if($trend && $trendValue)
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium px-2 py-0.5 rounded-full {{ $trendColor }} shrink-0">
                        @if($trend === 'up')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        @endif
                        {{ $trendValue }}
                    </span>
                @endif
            </div>
            @if($subtitle)
                <p class="text-xs text-gray-400 mt-1 truncate">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</div>
