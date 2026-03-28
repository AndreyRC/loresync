<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-bolt text-sm text-interactive"></i>
            <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Create Ability') }}</h1>
        </div>
    </x-slot>

    <x-library.entity-form type="abilities" :available-tags="$availableTags" />
</x-app-layout>
