@props([
    'name',
    'label' => null,
    'icon' => null,
    'helper' => null,
    'errorName' => null,
    'messages' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
])

@php
    $resolvedId = $id ?? $name;
    $resolvedErrorName = $errorName ?? $name;
    $resolvedMessages = $messages ?? $errors->get($resolvedErrorName);
    $hasError = !empty($resolvedMessages);

    $base = 'ls-focus block w-full rounded-xl border bg-surface text-slate-200 placeholder:text-slate-500 shadow-sm transition py-2 pr-3 leading-5';
    $padding = $icon ? 'pl-10' : 'pl-3';
    $state = $hasError
        ? 'border-red-500 hover:border-red-400 focus:border-red-500 focus:ring-red-500/30'
        : 'border-border hover:border-slate-600 focus:border-primary focus:ring-primary/30';
    $disabled = 'disabled:opacity-60 disabled:cursor-not-allowed';

    $inputClasses = trim("$base $padding $state $disabled");

    $shouldRenderValue = !in_array($type, ['password', 'file'], true);
    $resolvedValue = old($name, $value);
@endphp

<div class="space-y-1">
    @if ($label)
        <x-input-label for="{{ $resolvedId }}" :value="$label" />
    @endif

    <div class="relative group">
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-slate-500 transition group-focus-within:text-slate-300">
                <x-lucide :name="$icon" class="h-4 w-4" />
            </div>
        @endif

        <input
            id="{{ $resolvedId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if($shouldRenderValue)
                value="{{ $resolvedValue }}"
            @endif
            @if($hasError)
                aria-invalid="true"
            @endif
            {{ $attributes->merge(['class' => $inputClasses]) }}
        />
    </div>

    @if ($helper)
        <p class="text-xs text-slate-400">{{ $helper }}</p>
    @endif

    <x-input-error :messages="$resolvedMessages" />
</div>
