@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-400 text-sm font-medium leading-5 text-on-surface focus:outline-none focus:border-brand-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-on-surface-muted hover:text-on-surface-secondary hover:border-theme focus:outline-none focus:text-on-surface-secondary focus:border-theme transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
