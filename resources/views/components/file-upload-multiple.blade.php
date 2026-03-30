@props([
    'name',
    'id' => null,
    'accept' => '.jpg,.jpeg,.png,.pdf',
    'hint' => null,
    'label' => null,
    'existing' => [],
])

@php
    $inputId = $id ?? 'upload_' . str_replace(['[',']','.'], '_', $name);
    $existing = $existing ?? [];
@endphp

<div x-data="{
        dragOver: false,
        files: [],
        existing: @js(collect($existing)->map(fn($p) => ['path' => $p, 'name' => basename($p), 'url' => asset('storage/' . $p)])->values()),
        removedExisting: [],

        handleFiles(fileList) {
            for (const file of fileList) {
                const imageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                const entry = { file, name: file.name, preview: null };
                if (imageTypes.includes(file.type)) {
                    const reader = new FileReader();
                    reader.onload = (e) => { entry.preview = e.target.result; };
                    reader.readAsDataURL(file);
                }
                this.files.push(entry);
            }
            this.syncInput();
        },

        handleDrop(e) {
            this.dragOver = false;
            this.handleFiles(e.dataTransfer.files);
        },

        handleChange(e) {
            this.handleFiles(e.target.files);
            e.target.value = '';
        },

        removeFile(index) {
            this.files.splice(index, 1);
            this.syncInput();
        },

        removeExisting(index) {
            this.removedExisting.push(this.existing[index].path);
            this.existing.splice(index, 1);
        },

        syncInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f.file));
            this.$refs.fileInput.files = dt.files;
        }
     }">
    @if($label)
        <label class="block text-sm font-medium text-on-surface-secondary mb-1.5">{{ $label }}</label>
    @endif

    {{-- Existing files grid --}}
    <template x-if="existing.length > 0">
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mb-2">
            <template x-for="(item, i) in existing" :key="'ex-'+i">
                <div class="relative group">
                    <template x-if="item.name.match(/\.(jpg|jpeg|png|webp|gif)$/i)">
                        <img :src="item.url" :alt="item.name" class="h-20 w-full rounded-lg border border-theme object-cover">
                    </template>
                    <template x-if="!item.name.match(/\.(jpg|jpeg|png|webp|gif)$/i)">
                        <div class="h-20 w-full rounded-lg border border-theme bg-surface-hover flex flex-col items-center justify-center px-1">
                            <svg class="w-6 h-6 text-on-surface-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="text-[10px] text-on-surface-muted truncate w-full text-center mt-0.5" x-text="item.name"></span>
                        </div>
                    </template>
                    <button type="button" @click="removeExisting(i)"
                            class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 hover:bg-red-600 flex items-center justify-center text-white text-xs transition shadow-sm opacity-0 group-hover:opacity-100">
                        &times;
                    </button>
                </div>
            </template>
        </div>
    </template>

    {{-- New files preview grid --}}
    <template x-if="files.length > 0">
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mb-2">
            <template x-for="(item, i) in files" :key="'new-'+i">
                <div class="relative group">
                    <template x-if="item.preview">
                        <img :src="item.preview" :alt="item.name" class="h-20 w-full rounded-lg border border-brand-200 dark:border-gray-600 object-cover">
                    </template>
                    <template x-if="!item.preview">
                        <div class="h-20 w-full rounded-lg border border-brand-200 dark:border-gray-600 bg-brand-50/30 dark:bg-brand-950/30 flex flex-col items-center justify-center px-1">
                            <svg class="w-6 h-6 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="text-[10px] text-on-surface-muted truncate w-full text-center mt-0.5" x-text="item.name"></span>
                        </div>
                    </template>
                    <button type="button" @click="removeFile(i)"
                            class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 hover:bg-red-600 flex items-center justify-center text-white text-xs transition shadow-sm opacity-0 group-hover:opacity-100">
                        &times;
                    </button>
                </div>
            </template>
        </div>
    </template>

    {{-- Drop zone --}}
    <label :for="'{{ $inputId }}'"
           class="flex flex-col items-center justify-center w-full py-5 border-2 border-dashed rounded-lg cursor-pointer transition"
           :class="dragOver ? 'border-brand-400 bg-brand-50/50 dark:bg-brand-950/30' : 'border-theme bg-surface-hover/50 hover:bg-surface-alt/50'"
           x-on:dragover.prevent="dragOver = true"
           x-on:dragleave.prevent="dragOver = false"
           x-on:drop.prevent="handleDrop($event)">
        <svg class="w-6 h-6 mb-1.5 text-on-surface-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <p class="text-xs text-on-surface-muted"><span class="font-semibold text-brand-600 dark:text-brand-400">Cliquer pour telecharger</span> ou glisser-deposer</p>
        <p class="mt-0.5 text-xs text-on-surface-faint">Plusieurs fichiers possibles</p>
        <p class="mt-0.5 text-xs text-on-surface-faint">{{ $hint ?? 'JPG, PNG ou PDF (max 5 Mo chacun)' }}</p>
    </label>

    <input type="file" name="{{ $name }}[]" id="{{ $inputId }}" accept="{{ $accept }}" class="hidden" x-ref="fileInput"
           x-on:change="handleChange($event)" multiple>

    {{-- Hidden inputs for removed existing files --}}
    <template x-for="(path, i) in removedExisting" :key="'rm-'+i">
        <input type="hidden" name="remove_inspection_files[]" :value="path">
    </template>

    @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    @error($name . '.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
