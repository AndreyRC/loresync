@props([
    'type',
    'entity' => null,
    'tags' => [],
    'title' => null,
    'submitLabel' => null,
])

@php
    $isEditing = $entity !== null;
    $action = $isEditing ? route($type.'.update', $entity) : route($type.'.store');
    $method = $isEditing ? 'PUT' : 'POST';

    $resolvedTitle = $title ?? ($isEditing ? __('Edit') : __('Create'));
    $resolvedSubmitLabel = $submitLabel ?? ($isEditing ? __('Save') : __('Create'));

    $initialTags = collect($tags)
        ->map(fn ($t) => is_string($t) ? $t : '')
        ->filter(fn ($t) => $t !== '')
        ->values()
        ->all();
@endphp

<x-card>
    @if ($resolvedTitle)
        <h2 class="text-sm font-semibold text-slate-100">{{ $resolvedTitle }}</h2>
        <div class="mt-4 border-t border-border/70"></div>
    @endif

    <form
        class="mt-4 space-y-5"
        method="POST"
        action="{{ $action }}"
        enctype="multipart/form-data"
        x-data="{
            tags: @js($initialTags),
            newTag: '',
            addTag() {
                const cleaned = (this.newTag || '').trim();
                if (!cleaned) return;
                if (this.tags.includes(cleaned)) { this.newTag = ''; return; }
                this.tags.push(cleaned);
                this.newTag = '';
            },
            removeTag(index) {
                this.tags.splice(index, 1);
            },
        }"
    >
        @csrf
        @method($method)

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $entity?->name)"
                required
                autofocus
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Description')" />
            <textarea
                id="description"
                name="description"
                rows="5"
                class="ls-focus mt-1 block w-full rounded-xl border-border bg-app-bg/60 text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-interactive focus:ring-interactive/50"
            >{{ old('description', $entity?->description) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div>
            <x-input-label for="image" :value="__('Image')" />
            <input
                id="image"
                name="image"
                type="file"
                accept="image/png,image/jpeg"
                class="mt-1 block w-full text-sm text-slate-200 file:mr-4 file:rounded-xl file:border-0 file:bg-surface/70 file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-widest file:text-slate-200 hover:file:bg-surface"
            />
            <x-input-error class="mt-2" :messages="$errors->get('image')" />

            @if ($isEditing && $entity?->image_path)
                <div class="mt-3 flex items-center gap-3">
                    <img
                        src="{{ asset('storage/'.$entity->image_path) }}"
                        alt=""
                        class="h-12 w-12 rounded-xl object-cover ring-1 ring-inset ring-border"
                    />
                    <p class="text-xs text-slate-400">{{ __('Current image') }}</p>
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="tags" :value="__('Tags')" />

            <div class="mt-1 rounded-xl border border-border bg-app-bg/60 p-3">
                <div class="flex flex-wrap gap-2">
                    <template x-for="(tag, idx) in tags" :key="tag">
                        <span class="inline-flex items-center gap-2 rounded-full bg-primary/15 px-3 py-1 text-xs font-medium text-slate-100 ring-1 ring-inset ring-primary/30">
                            <span x-text="tag"></span>
                            <button type="button" class="text-slate-200/80 hover:text-slate-50" @click="removeTag(idx)">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                            <input type="hidden" name="tags[]" :value="tag">
                        </span>
                    </template>
                </div>

                <div class="mt-3 flex items-center gap-2">
                    <input
                        id="tags"
                        type="text"
                        class="ls-focus w-full rounded-xl border-border bg-surface/40 text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-interactive focus:ring-interactive/50"
                        placeholder="{{ __('Type a tag and press Enter') }}"
                        x-model="newTag"
                        @keydown.enter.prevent="addTag()"
                    />
                    <x-secondary-button type="button" @click="addTag()">{{ __('Add') }}</x-secondary-button>
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('tags')" />
            <x-input-error class="mt-2" :messages="$errors->get('tags.*')" />
        </div>

        <div class="flex items-center justify-end gap-3">
            <x-secondary-button type="button" onclick="history.back()">{{ __('Cancel') }}</x-secondary-button>
            <x-primary-button>{{ $resolvedSubmitLabel }}</x-primary-button>
        </div>
    </form>
</x-card>
