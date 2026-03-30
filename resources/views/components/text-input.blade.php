@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'h-11 w-full rounded-lg border border-theme bg-transparent px-4 py-2.5 text-sm text-on-surface placeholder:text-on-surface-faint focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden disabled:bg-surface-alt disabled:cursor-not-allowed']) }}>
