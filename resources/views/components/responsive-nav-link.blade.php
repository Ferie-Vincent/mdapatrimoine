@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-400 text-start text-base font-medium text-brand-700 bg-brand-50 dark:bg-brand-950/30 focus:outline-none focus:text-brand-800 focus:bg-brand-100 dark:bg-brand-900/40 focus:border-brand-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-on-surface-secondary hover:text-on-surface hover:bg-surface-hover hover:border-theme focus:outline-none focus:text-on-surface focus:bg-surface-hover focus:border-theme transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
