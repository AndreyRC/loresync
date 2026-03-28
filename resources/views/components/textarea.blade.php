@props([
    'name',
    'label' => null,
    'icon' => null,
    'helper' => null,
    'errorName' => null,
    'messages' => null,
    'id' => null,
    'rows' => 5,
    'value' => null,
])

@php
    $resolvedId = $id ?? $name;
    $resolvedErrorName = $errorName ?? $name;
    $resolvedMessages = $messages ?? $errors->get($resolvedErrorName);
    $hasError = !empty($resolvedMessages);

    $base = 'ls-focus block w-full rounded-xl border bg-surface text-slate-200 placeholder:text-slate-500 shadow-sm transition py-2 pr-3 leading-5';
    $padding = $icon ? 'pl-12' : 'pl-3';
    $state = $hasError
        ? 'border-red-500 hover:border-red-400 focus:border-red-500 focus:ring-red-500/30'
        : 'border-border hover:border-slate-600 focus:border-primary focus:ring-primary/30';
    $disabled = 'disabled:opacity-60 disabled:cursor-not-allowed';

    $textareaClasses = trim("$base $padding $state $disabled");

    $resolvedValue = old($name, $value);
@endphp

<div class="space-y-1">
    @if ($label)
        <x-input-label for="{{ $resolvedId }}" :value="$label" />
    @endif

    <div class="relative group">
        @if ($icon)
            <div class="pointer-events-none absolute left-0 top-0 flex h-10 w-11 items-center justify-center text-slate-500 transition group-focus-within:text-slate-300">
                <x-lucide :name="$icon" class="h-4 w-4" />
            </div>
        @endif

        <textarea
            id="{{ $resolvedId }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            @if($hasError)
                aria-invalid="true"
            @endif
            {{ $attributes->merge(['class' => $textareaClasses]) }}
        >{{ $resolvedValue }}</textarea>
    </div>

    @if ($helper)
        <p class="text-xs text-slate-400">{{ $helper }}</p>
    @endif

    <x-input-error :messages="$resolvedMessages" />
</div>
