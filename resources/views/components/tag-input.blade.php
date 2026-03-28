@props([
    'name' => 'tags',
    'label' => null,
    'icon' => 'tag',
    'helper' => null,
    'availableTags' => [],
    'value' => [],
    'id' => null,
])

@php
    $resolvedId = $id ?? $name;
    $initialTags = collect($value)
        ->map(fn ($t) => is_string($t) ? trim($t) : '')
        ->filter()
        ->values()
        ->all();
@endphp

<div
    class="space-y-1"
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
    @if ($label)
        <x-input-label for="{{ $resolvedId }}" :value="$label" />
    @endif

    <div class="rounded-xl border border-border bg-surface p-3">
        <div class="flex flex-wrap gap-2">
            <template x-for="(tag, idx) in tags" :key="tag">
                <span class="inline-flex items-center gap-2 rounded-full bg-border px-3 py-1 text-xs font-medium text-slate-200">
                    <span x-text="tag"></span>
                    <button type="button" class="text-slate-300 hover:text-slate-50" @click="removeTag(idx)">
                        <span class="sr-only">{{ __('Remove') }}</span>
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </button>
                    <input type="hidden" name="{{ $name }}[]" :value="tag">
                </span>
            </template>
        </div>

        @if (count($availableTags))
            <div class="mt-3">
                <x-select
                    name=""
                    :label="__('Existing tags')"
                    icon="tag"
                    @change="if ($event.target.value) { newTag = $event.target.value; addTag(); $event.target.value = ''; }"
                >
                    <option value="" selected>{{ __('Select a tag...') }}</option>
                    @foreach ($availableTags as $tagName)
                        <option value="{{ $tagName }}">{{ $tagName }}</option>
                    @endforeach
                </x-select>
            </div>
        @endif

        <div class="mt-3 flex items-end gap-2">
            <div class="flex-1">
                <x-input
                    name="{{ $resolvedId }}_new"
                    icon="tag"
                    placeholder="{{ __('Type and press Enter') }}"
                    x-model="newTag"
                    @keydown.enter.prevent="addTag()"
                />
            </div>

            <x-secondary-button type="button" class="shrink-0" @click="addTag()">{{ __('Add') }}</x-secondary-button>
        </div>
    </div>

    @if ($helper)
        <p class="text-xs text-slate-400">{{ $helper }}</p>
    @endif

    <x-input-error :messages="$errors->get($name)" />
    <x-input-error :messages="$errors->get($name.'.*')" />
</div>
