@props([
    'name' => null,
    'label' => null,
    'icon' => null,
    'helper' => null,
    'errorName' => null,
    'messages' => null,
    'id' => null,
])

@php
    $resolvedId = $id ?? ($name ?: 'select_'.
        substr(md5(json_encode([$attributes->get('wire:model'), $attributes->get('x-model'), $attributes->get('x-bind:value')])), 0, 8)
    );

    $resolvedErrorName = $errorName ?? $name;
    $resolvedMessages = $messages ?? ($resolvedErrorName ? $errors->get($resolvedErrorName) : []);
    $hasError = !empty($resolvedMessages);

    $base = 'ls-focus block w-full appearance-none rounded-xl border bg-surface text-slate-200 shadow-sm transition py-2 pr-10 leading-5';
    $padding = $icon ? 'pl-10' : 'pl-3';
    $state = $hasError
        ? 'border-red-500 hover:border-red-400 focus:border-red-500 focus:ring-red-500/30'
        : 'border-border hover:border-slate-600 focus:border-primary focus:ring-primary/30';
    $disabled = 'disabled:opacity-60 disabled:cursor-not-allowed';

    $selectClasses = trim("$base $padding $state $disabled");
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

        <select
            id="{{ $resolvedId }}"
            @if($name)
                name="{{ $name }}"
            @endif
            @if($hasError)
                aria-invalid="true"
            @endif
            {{ $attributes->merge(['class' => $selectClasses]) }}
        >
            {{ $slot }}
        </select>

        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 transition group-focus-within:text-slate-300">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    @if ($helper)
        <p class="text-xs text-slate-400">{{ $helper }}</p>
    @endif

    @if (!empty($resolvedMessages))
        <x-input-error :messages="$resolvedMessages" />
    @endif
</div>
