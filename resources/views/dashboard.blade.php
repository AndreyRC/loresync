<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-table-columns text-sm text-interactive"></i>
            <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Dashboard') }}</h1>
        </div>
    </x-slot>

    <x-card>
        <p class="text-sm text-slate-200">{{ __("You're logged in!") }}</p>
        <p class="mt-2 text-xs text-slate-400">{{ __('Use the sidebar to manage campaigns, sessions, NPCs, and locations.') }}</p>
    </x-card>
</x-app-layout>
