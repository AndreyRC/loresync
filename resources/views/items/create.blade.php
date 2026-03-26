<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-box text-sm text-interactive"></i>
            <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Create Item') }}</h1>
        </div>
    </x-slot>

    <x-library.entity-form type="items" />
</x-app-layout>
