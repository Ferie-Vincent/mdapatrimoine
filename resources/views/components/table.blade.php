@props([])

<div {{ $attributes->merge(['class' => 'bg-surface rounded-2xl border border-theme-subtle overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-theme-subtle">
            {{ $slot }}
        </table>
    </div>
</div>
