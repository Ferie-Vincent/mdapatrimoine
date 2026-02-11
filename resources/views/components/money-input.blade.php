@props([
    'name',
    'id' => null,
    'value' => '',
    'min' => null,
    'max' => null,
    'required' => false,
    'placeholder' => '0',
    'label' => null,
    'suffix' => 'FCFA',
    'class' => '',
])

@php
    $inputId = $id ?? $name;
    $oldValue = old($name, $value);
@endphp

<div x-data="moneyInput('{{ $oldValue }}', {{ $min ?? 'null' }}, {{ $max ?? 'null' }})" class="relative">
    @if($label)
        <label for="{{ $inputId }}_display" class="block text-sm font-medium text-gray-700 mb-1.5">{{ $label }}</label>
    @endif
    <div class="relative">
        <input type="text"
               id="{{ $inputId }}_display"
               x-model="display"
               x-on:input="onInput($event)"
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}
               inputmode="numeric"
               {{ $attributes->merge(['class' => 'mt-1.5 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden ' . ($suffix ? 'pr-16' : '') . ' ' . $class]) }}>
        @if($suffix)
            <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs font-medium text-gray-400">{{ $suffix }}</span>
        @endif
    </div>
    <input type="hidden" name="{{ $name }}" x-bind:value="rawValue">
</div>
