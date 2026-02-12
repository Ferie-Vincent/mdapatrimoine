@props([
    'type' => 'default',
    'label' => '',
])

@php
$classes = match($type) {
    'paye', 'success'       => 'bg-green-50 text-green-600 ring-green-500/20',
    'partiel', 'warning'    => 'bg-yellow-50 text-yellow-600 ring-yellow-500/20',
    'impaye', 'danger'      => 'bg-red-50 text-red-600 ring-red-500/20',
    'en_retard'             => 'bg-red-50 text-red-700 ring-red-500/20 font-semibold',
    'a_venir'               => 'bg-blue-50 text-blue-600 ring-blue-500/20',
    'actif'                 => 'bg-green-50 text-green-600 ring-green-500/20',
    'resilie'               => 'bg-gray-50 text-gray-600 ring-gray-500/20',
    'en_attente', 'default' => 'bg-blue-50 text-blue-600 ring-blue-500/20',
    'disponible'            => 'bg-green-50 text-green-600 ring-green-500/20',
    'occupe'                => 'bg-blue-50 text-blue-600 ring-blue-500/20',
    'travaux'               => 'bg-orange-50 text-orange-600 ring-orange-500/20',
    default                 => 'bg-gray-50 text-gray-600 ring-gray-500/20',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium ring-1 ring-inset {$classes}"]) }}>
    {{ $label ?: $slot }}
</span>
