<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-lg border border-theme bg-surface px-5 py-3 text-sm font-medium text-on-surface-secondary hover:bg-surface-hover transition']) }}>
    {{ $slot }}
</button>
