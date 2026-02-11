<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-lg bg-brand-600 px-5 py-3 text-sm font-medium text-white hover:bg-brand-700 transition shadow-sm']) }}>
    {{ $slot }}
</button>
