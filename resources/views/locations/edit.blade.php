<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-location-dot text-sm text-interactive"></i>
            <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Edit Location') }}</h1>
        </div>
    </x-slot>

    <x-library.entity-form
        type="locations"
        :entity="$location"
        :tags="$location->tags->pluck('name')->all()"
        :available-tags="$availableTags"
    />
</x-app-layout>
