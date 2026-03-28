@props([
    'name',
    'label' => null,
    'icon' => 'image',
    'helper' => null,
    'errorName' => null,
    'messages' => null,
    'id' => null,
    'accept' => null,
    'existingUrl' => null,
    'existingLabel' => null,
])

@php
    $resolvedId = $id ?? $name;
    $resolvedErrorName = $errorName ?? $name;
    $resolvedMessages = $messages ?? $errors->get($resolvedErrorName);
    $hasError = !empty($resolvedMessages);

    $dropBase = 'ls-focus flex w-full cursor-pointer items-center justify-between gap-3 rounded-xl border bg-surface px-3 py-3 text-left shadow-sm transition';
    $dropState = $hasError
        ? 'border-red-500 hover:border-red-400 focus:border-red-500 focus:ring-red-500/30'
        : 'border-border hover:border-slate-600 focus:border-primary focus:ring-primary/30';
    $dropDisabled = 'disabled:opacity-60 disabled:cursor-not-allowed';

    $dropClasses = trim("$dropBase $dropState $dropDisabled");

    $resolvedExistingLabel = $existingLabel ?? __('Current image');
@endphp

<div class="space-y-1" x-data="{
    previewUrl: @js($existingUrl),
    fileName: '',
    updatePreview(file) {
        if (!file) return;
        this.fileName = file.name || '';
        if (!file.type || !file.type.startsWith('image/')) {
            this.previewUrl = null;
            return;
        }
        this.previewUrl = URL.createObjectURL(file);
    },
}">
    @if ($label)
        <x-input-label for="{{ $resolvedId }}" :value="$label" />
    @endif

    <input
        id="{{ $resolvedId }}"
        name="{{ $name }}"
        type="file"
        @if($accept)
            accept="{{ $accept }}"
        @endif
        class="sr-only"
        @change="updatePreview($event.target.files?.[0])"
        {{ $attributes->except('class') }}
    />

    <label for="{{ $resolvedId }}" class="{{ $dropClasses }}">
        <div class="flex items-center gap-3">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-app-bg/40 text-slate-400">
                <x-lucide :name="$icon" class="h-4 w-4" />
            </span>

            <div class="min-w-0">
                <p class="text-sm font-medium text-slate-200">{{ __('Upload image') }}</p>
                <p class="mt-0.5 text-xs text-slate-400" x-text="fileName ? fileName : '{{ __('PNG or JPG') }}'"></p>
            </div>
        </div>

        <span class="inline-flex items-center gap-2 rounded-xl bg-app-bg/50 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200">
            <x-lucide name="upload" class="h-4 w-4" />
            {{ __('Choose') }}
        </span>
    </label>

    <template x-if="previewUrl">
        <div class="mt-2 flex items-center gap-3">
            <img :src="previewUrl" alt="" class="h-12 w-12 rounded-xl object-cover ring-1 ring-inset ring-border" />
            <p class="text-xs text-slate-400">{{ $resolvedExistingLabel }}</p>
        </div>
    </template>

    @if ($helper)
        <p class="text-xs text-slate-400">{{ $helper }}</p>
    @endif

    <x-input-error :messages="$resolvedMessages" />
</div>
