@props([
    'name' => 'photos',
    'existing' => [],
    'max' => 10,
])

@php
    $existing = $existing ?? [];
    $inputId = 'photo_upload_' . uniqid();
@endphp

<div x-data="{
        files: [],
        previews: [],
        existing: @js($existing),
        toDelete: [],
        max: {{ $max }},
        dragOver: false,

        get remaining() {
            return this.max - (this.existing.length - this.toDelete.length) - this.files.length;
        },

        addFiles(fileList) {
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];
            const maxSize = 5 * 1024 * 1024;

            for (const file of fileList) {
                if (this.remaining <= 0) break;
                if (!allowed.includes(file.type)) continue;
                if (file.size > maxSize) continue;

                this.files.push(file);

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previews.push({ name: file.name, url: e.target.result });
                };
                reader.readAsDataURL(file);
            }

            this.syncInput();
        },

        removeNew(index) {
            this.files.splice(index, 1);
            this.previews.splice(index, 1);
            this.syncInput();
        },

        toggleDelete(photo) {
            const idx = this.toDelete.indexOf(photo);
            if (idx === -1) {
                this.toDelete.push(photo);
            } else {
                this.toDelete.splice(idx, 1);
            }
        },

        isMarkedForDelete(photo) {
            return this.toDelete.includes(photo);
        },

        syncInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f));
            this.$refs.formInput.files = dt.files;
        },

        handleDrop(e) {
            this.dragOver = false;
            this.addFiles(e.dataTransfer.files);
        },

        handlePick(e) {
            this.addFiles(e.target.files);
            e.target.value = '';
        }
     }">

    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Photos</p>

    {{-- Main file input for form submission (never directly interacted with by user) --}}
    <input type="file" name="{{ $name }}[]" id="{{ $inputId }}" accept=".jpg,.jpeg,.png,.webp" multiple class="hidden"
           x-ref="formInput">

    {{-- Existing photos --}}
    <template x-if="existing.length > 0">
        <div class="mb-3">
            <p class="text-xs text-gray-500 mb-2">Photos actuelles</p>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                <template x-for="(photo, idx) in existing" :key="'existing-'+idx">
                    <div class="relative group rounded-lg overflow-hidden border"
                         :class="isMarkedForDelete(photo) ? 'border-red-300 opacity-50' : 'border-gray-200'">
                        <img :src="'/storage/' + photo" class="w-full h-20 object-cover" :alt="'Photo ' + (idx+1)">
                        <button type="button"
                                @click="toggleDelete(photo)"
                                class="absolute top-1 right-1 w-5 h-5 rounded-full flex items-center justify-center text-white text-xs transition"
                                :class="isMarkedForDelete(photo) ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'">
                            <span x-text="isMarkedForDelete(photo) ? '&#x21A9;' : '&times;'"></span>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- Hidden inputs for photos to delete --}}
    <template x-for="(photo, idx) in toDelete" :key="'del-'+idx">
        <input type="hidden" name="delete_photos[]" :value="photo">
    </template>

    {{-- New photo previews --}}
    <div x-show="previews.length > 0" class="mb-3">
        <p class="text-xs text-gray-500 mb-2">Nouvelles photos</p>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
            <template x-for="(preview, idx) in previews" :key="'new-'+idx">
                <div class="relative group rounded-lg overflow-hidden border border-brand-200">
                    <img :src="preview.url" class="w-full h-20 object-cover" :alt="preview.name">
                    <button type="button"
                            @click="removeNew(idx)"
                            class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 hover:bg-red-600 flex items-center justify-center text-white text-xs transition">
                        &times;
                    </button>
                </div>
            </template>
        </div>
    </div>

    {{-- Picker input (visually hidden but still clickable programmatically) --}}
    <input type="file" accept=".jpg,.jpeg,.png,.webp" multiple
           class="absolute w-0 h-0 overflow-hidden opacity-0"
           x-ref="picker" x-on:change="handlePick($event)">

    {{-- Drop zone --}}
    <div x-show="remaining > 0"
         @click="$refs.picker.click()"
         class="flex flex-col items-center justify-center w-full py-4 border-2 border-dashed rounded-lg cursor-pointer transition"
         :class="dragOver ? 'border-brand-400 bg-brand-50/50' : 'border-gray-300 bg-gray-50/50 hover:bg-gray-100/50'"
         x-on:dragover.prevent="dragOver = true"
         x-on:dragleave.prevent="dragOver = false"
         x-on:drop.prevent="handleDrop($event)">
        <svg class="w-6 h-6 mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <p class="text-xs text-gray-500"><span class="font-semibold text-brand-600">Cliquer pour ajouter</span> ou glisser-deposer</p>
        <p class="mt-0.5 text-xs text-gray-400">JPG, PNG ou WebP (max 5 Mo par photo, <span x-text="remaining"></span> restante<span x-show="remaining > 1">s</span>)</p>
    </div>

    <p x-show="remaining <= 0" class="text-xs text-gray-500 italic">Nombre maximum de photos atteint ({{ $max }}).</p>

    @error('photos')   <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    @error('photos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
