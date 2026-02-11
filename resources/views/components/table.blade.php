@props([])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-gray-100 overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            {{ $slot }}
        </table>
    </div>
</div>
