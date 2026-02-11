@props([
    'name',
    'id' => null,
    'accept' => '.jpg,.jpeg,.png,.pdf',
    'hint' => null,
    'label' => null,
    'current' => null,
])

@php
    $inputId = $id ?? 'upload_' . str_replace(['[',']','.'], '_', $name);
@endphp

<div x-data="{
        dragOver: false,
        fileName: '',
        previewUrl: null,

        handleFile(file) {
            if (!file) return;
            this.fileName = file.name;
            const imageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (imageTypes.includes(file.type)) {
                const reader = new FileReader();
                reader.onload = (e) => { this.previewUrl = e.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.previewUrl = null;
            }
        },

        handleDrop(e) {
            this.dragOver = false;
            this.$refs.fileInput.files = e.dataTransfer.files;
            this.handleFile(e.dataTransfer.files[0]);
        },

        handleChange(e) {
            this.handleFile(e.target.files[0]);
        },

        removeFile() {
            this.fileName = '';
            this.previewUrl = null;
            this.$refs.fileInput.value = '';
        }
     }">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $label }}</label>
    @endif
    @if($current)
        <p class="mb-1.5 text-xs text-gray-500">Fichier actuel : {{ $current }}</p>
    @endif

    {{-- Image preview --}}
    <div x-show="previewUrl" x-cloak class="mb-2 relative inline-block">
        <img :src="previewUrl" alt="Apercu" class="h-28 w-auto rounded-lg border border-gray-200 object-cover shadow-sm">
        <button type="button" @click="removeFile()"
                class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 hover:bg-red-600 flex items-center justify-center text-white text-xs transition shadow-sm">
            &times;
        </button>
    </div>

    <label :for="'{{ $inputId }}'"
           x-show="!previewUrl"
           class="flex flex-col items-center justify-center w-full py-5 border-2 border-dashed rounded-lg cursor-pointer transition"
           :class="dragOver ? 'border-brand-400 bg-brand-50/50' : 'border-gray-300 bg-gray-50/50 hover:bg-gray-100/50'"
           x-on:dragover.prevent="dragOver = true"
           x-on:dragleave.prevent="dragOver = false"
           x-on:drop.prevent="handleDrop($event)">
        <svg class="w-6 h-6 mb-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <p class="text-xs text-gray-500" x-show="!fileName"><span class="font-semibold text-brand-600">Cliquer pour telecharger</span> ou glisser-deposer</p>
        <p class="text-xs font-medium text-brand-700" x-show="fileName" x-text="fileName" x-cloak></p>
        <p class="mt-1 text-xs text-gray-400">{{ $hint ?? 'JPG, PNG ou PDF (max 5 Mo)' }}</p>
    </label>

    {{-- Non-image file name display --}}
    <div x-show="fileName && !previewUrl" x-cloak class="mt-2 flex items-center gap-2 text-sm text-gray-600">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span x-text="fileName" class="truncate"></span>
        <button type="button" @click="removeFile()" class="text-red-400 hover:text-red-600 transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <input type="file" name="{{ $name }}" id="{{ $inputId }}" accept="{{ $accept }}" class="hidden" x-ref="fileInput"
           x-on:change="handleChange($event)">
    @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
