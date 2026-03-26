<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-box text-sm text-interactive"></i>
                <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Items') }}</h1>
            </div>

            <a href="{{ route('items.create') }}" class="ls-focus inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-primary-hover">
                <i class="fa-solid fa-plus text-xs"></i>
                {{ __('New Item') }}
            </a>
        </div>
    </x-slot>

    <x-card>
        <form method="GET" action="{{ route('items.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <x-input-label for="tag" :value="__('Filter by tag')" />
                <x-text-input id="tag" name="tag" type="text" class="mt-1 block w-full" :value="old('tag', $tag)" placeholder="{{ __('e.g. weapon') }}" />
            </div>
            <div class="flex items-center gap-3">
                <x-primary-button type="submit">{{ __('Filter') }}</x-primary-button>
                <a href="{{ route('items.index') }}" class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70">
                    {{ __('Clear') }}
                </a>
            </div>
        </form>
    </x-card>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($items as $item)
            <x-library.entity-card type="items" :entity="$item" :campaigns="$campaigns" icon="fa-solid fa-box" />
        @empty
            <x-card>
                <p class="text-sm text-slate-200">{{ __('No items yet.') }}</p>
                <p class="mt-2 text-xs text-slate-400">{{ __('Create your first item to start building your global library.') }}</p>
            </x-card>
        @endforelse
    </div>
</x-app-layout>
