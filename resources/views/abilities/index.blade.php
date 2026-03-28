<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-bolt text-sm text-interactive"></i>
                <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Abilities') }}</h1>
            </div>

            <a href="{{ route('abilities.create') }}" class="ls-focus inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-primary-hover">
                <i class="fa-solid fa-plus text-xs"></i>
                {{ __('New Ability') }}
            </a>
        </div>
    </x-slot>

    <x-card>
        <form
            method="GET"
            action="{{ route('abilities.index') }}"
            class="flex flex-col gap-3 sm:flex-row sm:items-end"
            x-data="{ tagValue: @js(old('tag', $tag) ?? ''), typeValue: @js(old('type', $type) ?? '') }"
        >
            <div class="flex-1">
                <x-input-label for="tag_select" :value="__('Existing tags')" />
                <select
                    id="tag_select"
                    class="ls-focus mt-1 block w-full rounded-xl border-border bg-app-bg/60 text-slate-100 shadow-sm focus:border-interactive focus:ring-interactive/50"
                    @change="tagValue = $event.target.value"
                >
                    <option value="">{{ __('Select...') }}</option>
                    @foreach ($availableTags as $tagName)
                        <option value="{{ $tagName }}" @selected(($tagName === ($tag ?? '')))
                        >{{ $tagName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <x-input-label for="tag" :value="__('Or type a tag')" />
                <x-text-input
                    id="tag"
                    name="tag"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="{{ __('e.g. fire') }}"
                    x-model="tagValue"
                />
            </div>

            <div class="flex-1">
                <x-input-label for="type_select" :value="__('Existing types')" />
                <select
                    id="type_select"
                    class="ls-focus mt-1 block w-full rounded-xl border-border bg-app-bg/60 text-slate-100 shadow-sm focus:border-interactive focus:ring-interactive/50"
                    @change="typeValue = $event.target.value"
                >
                    <option value="">{{ __('Select...') }}</option>
                    @foreach ($availableTypes as $availableType)
                        <option value="{{ $availableType }}" @selected(($availableType === ($type ?? '')))
                        >{{ $availableType }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <x-input-label for="type" :value="__('Or type a type')" />
                <x-text-input
                    id="type"
                    name="type"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="{{ __('e.g. spell') }}"
                    x-model="typeValue"
                />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button type="submit">{{ __('Filter') }}</x-primary-button>
                <a href="{{ route('abilities.index') }}" class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70">
                    {{ __('Clear') }}
                </a>
            </div>
        </form>
    </x-card>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($abilities as $ability)
            @php
                $tags = $ability->tags ?? collect();
                $attrs = $ability->abilityAttributes ?? collect();
                $preview = $attrs->take(3);
            @endphp

            <x-card class="p-0 overflow-hidden">
                <div class="h-36 w-full bg-surface/60">
                    @if ($ability->image_path)
                        <img
                            src="{{ asset('storage/'.$ability->image_path) }}"
                            alt=""
                            class="h-36 w-full object-cover"
                        />
                    @else
                        <div class="flex h-36 w-full items-center justify-center bg-surface/40">
                            <i class="fa-solid fa-bolt text-2xl text-slate-400"></i>
                        </div>
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-100">{{ $ability->name }}</h3>

                            @if ($ability->type)
                                <p class="mt-1 text-xs text-slate-300">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-primary/15 px-2 py-0.5 text-[11px] font-medium text-slate-100 ring-1 ring-inset ring-primary/30">
                                        <i class="fa-solid fa-tag text-[11px]"></i>
                                        {{ $ability->type }}
                                    </span>
                                </p>
                            @endif

                            @if ($ability->description)
                                <p class="mt-1 text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($ability->description, 110) }}</p>
                            @endif

                            @if ($preview->count())
                                <div class="mt-3 space-y-1 text-xs">
                                    @foreach ($preview as $attribute)
                                        <div class="flex items-baseline justify-between gap-3">
                                            <span class="text-slate-200">{{ \Illuminate\Support\Str::headline($attribute->key) }}</span>
                                            <span class="text-slate-400">{{ $attribute->value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <a
                            href="{{ route('abilities.edit', $ability) }}"
                            class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-surface/40 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface"
                        >
                            {{ __('Edit') }}
                        </a>
                    </div>

                    @if ($tags->count())
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                                <span class="inline-flex items-center rounded-full bg-primary/15 px-3 py-1 text-[11px] font-medium text-slate-100 ring-1 ring-inset ring-primary/30">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-5 flex items-center justify-end gap-3">
                        <form method="POST" action="{{ route('abilities.destroy', $ability) }}" onsubmit="return confirm('{{ __('Delete this ability?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-rose-200 transition hover:bg-rose-500/10">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </x-card>
        @empty
            <x-card>
                <p class="text-sm text-slate-200">{{ __('No abilities yet.') }}</p>
                <p class="mt-2 text-xs text-slate-400">{{ __('Create your first ability to start building your global library.') }}</p>
            </x-card>
        @endforelse
    </div>
</x-app-layout>
