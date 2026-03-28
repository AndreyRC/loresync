<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-users text-sm text-interactive"></i>
                <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('NPCs') }}</h1>
            </div>

            <a href="{{ route('npcs.create') }}" class="ls-focus inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-primary-hover">
                <i class="fa-solid fa-plus text-xs"></i>
                {{ __('New NPC') }}
            </a>
        </div>
    </x-slot>

    <x-card>
        <form
            method="GET"
            action="{{ route('npcs.index') }}"
            class="flex flex-col gap-3 sm:flex-row sm:items-end"
            x-data="{ tagValue: @js(old('tag', $tag) ?? '') }"
        >
            <div class="flex-1">
                <x-select
                    id="tag_select"
                    name="tag_select"
                    :label="__('Existing tags')"
                    icon="tag"
                    @change="tagValue = $event.target.value"
                >
                    <option value="">{{ __('Select...') }}</option>
                    @foreach ($availableTags as $tagName)
                        <option value="{{ $tagName }}" @selected(($tagName === ($tag ?? '')))
                        >{{ $tagName }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="flex-1">
                <x-select
                    id="status"
                    name="status"
                    :label="__('Status')"
                    icon="tag"
                >
                    @php
                        $selectedStatus = old('status', $status ?? '');
                    @endphp
                    <option value="">{{ __('Any status') }}</option>
                    <option value="alive" @selected($selectedStatus === 'alive')>{{ __('Alive') }}</option>
                    <option value="dead" @selected($selectedStatus === 'dead')>{{ __('Dead') }}</option>
                    <option value="missing" @selected($selectedStatus === 'missing')>{{ __('Missing') }}</option>
                    <option value="unknown" @selected($selectedStatus === 'unknown')>{{ __('Unknown') }}</option>
                </x-select>
            </div>

            <div class="flex-1">
                <x-input
                    id="tag"
                    name="tag"
                    type="text"
                    :label="__('Or type a tag')"
                    icon="tag"
                    placeholder="{{ __('e.g. villain') }}"
                    x-model="tagValue"
                />
            </div>
            <div class="flex items-center gap-3">
                <x-primary-button type="submit">{{ __('Filter') }}</x-primary-button>
                <a href="{{ route('npcs.index') }}" class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70">
                    {{ __('Clear') }}
                </a>
            </div>
        </form>
    </x-card>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($npcs as $npc)
            <x-library.entity-card type="npcs" :entity="$npc" :campaigns="$campaigns" icon="fa-solid fa-users" />
        @empty
            <x-card>
                <p class="text-sm text-slate-200">{{ __('No NPCs yet.') }}</p>
                <p class="mt-2 text-xs text-slate-400">{{ __('Create your first NPC to start building your global library.') }}</p>
            </x-card>
        @endforelse
    </div>
</x-app-layout>
