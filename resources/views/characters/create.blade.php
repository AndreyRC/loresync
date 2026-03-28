<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-user-group text-sm text-interactive"></i>
            <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Create Character') }}</h1>
        </div>
    </x-slot>

    <x-library.entity-form
        type="characters"
        :available-tags="$availableTags"
        :available-characters="$availableCharacters"
        :available-players="$availablePlayers"
        :available-campaigns="$availableCampaigns"
    />
</x-app-layout>
