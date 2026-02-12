@props([
    'name',
    'title' => '',
    'action' => '',
    'method' => 'POST',
    'maxWidth' => '2xl',
    'submitLabel' => 'Enregistrer',
    'hasFiles' => false,
    'icon' => '',
    'iconColor' => 'text-brand-500',
])

<div
    x-data="{
        show: false,
        loading: false,
        errors: {},
        successMessage: '',
        open(data) {
            this.errors = {};
            this.successMessage = '';
            this.loading = false;
            this.show = true;
        },
        close() {
            this.show = false;
            this.errors = {};
        },
        async submit(e) {
            e.preventDefault();
            this.loading = true;
            this.errors = {};

            const form = e.target;

            await window.offlineAwareSubmit(form, {
                description: @js($title),
                onSuccess: () => { this.close(); window.location.reload(); },
                onValidationError: (result) => {
                    this.errors = result.errors;
                    const messages = Object.keys(result.errors).flatMap(f => result.errors[f]);
                    window.toast(messages.join('<br>'), 'error');
                },
                onError: (msg) => { this.errors = { _general: [msg] }; window.toast(msg, 'error'); },
                onQueued: () => { this.close(); },
            });

            this.loading = false;
        }
    }"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? open($event.detail) : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? close() : null"
    x-on:keydown.escape.window="close()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div x-show="show" class="fixed inset-0 transform transition-all" @click="close()"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    @php
    $maxWidthClass = [
        'sm'  => 'sm:max-w-sm',
        'md'  => 'sm:max-w-md',
        'lg'  => 'sm:max-w-lg',
        'xl'  => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
    ][$maxWidth] ?? 'sm:max-w-2xl';
    @endphp

    {{-- Modal card --}}
    <div x-show="show"
         class="mb-6 bg-white rounded-2xl overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidthClass }} sm:mx-auto max-h-[90vh] flex flex-col"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                @if($icon)<span class="{{ $iconColor }}">{!! $icon !!}</span>@endif
                {{ $title }}
            </h3>
            <button @click="close()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- General error --}}
        <template x-if="errors._general">
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                <template x-for="err in errors._general"><p x-text="err"></p></template>
            </div>
        </template>

        {{-- Form --}}
        <form action="{{ $action }}" @submit="submit($event)" {{ $hasFiles ? 'enctype=multipart/form-data' : '' }} class="flex flex-col flex-1 overflow-hidden">
            @if($method !== 'POST')
                <input type="hidden" name="_method" value="{{ $method }}">
            @endif

            {{-- Scrollable content --}}
            <div class="p-6 overflow-y-auto flex-1">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 shrink-0 bg-gray-50">
                <button type="button" @click="close()" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit" :disabled="loading"
                        class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Enregistrement...' : @js($submitLabel)"></span>
                </button>
            </div>
        </form>
    </div>
</div>
