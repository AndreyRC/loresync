@props([
    'type',
    'entity',
    'campaigns' => [],
    'icon' => 'fa-solid fa-circle',
])

@php
    $modalName = 'add-to-campaign-'.$type.'-'.$entity->id;
    $tags = $entity->tags ?? collect();
    $actionHref = $type === 'characters'
        ? route('characters.show', $entity)
        : route($type.'.edit', $entity);
    $actionLabel = $type === 'characters' ? __('View') : __('Edit');
@endphp

<x-card class="p-0 overflow-hidden">
    <div class="h-36 w-full bg-surface/60">
        @if ($entity->image_path)
            <img
                src="{{ asset('storage/'.$entity->image_path) }}"
                alt=""
                class="h-36 w-full object-cover"
            />
        @else
            <div class="flex h-36 w-full items-center justify-center bg-surface/40">
                <i class="{{ $icon }} text-2xl text-slate-400"></i>
            </div>
        @endif
    </div>

    <div class="p-5">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-slate-100">{{ $entity->name }}</h3>

                @if ($type === 'characters' && $entity->type)
                    @php
                        $isPlayer = $entity->type === 'player';
                        $badgeStyles = $isPlayer
                            ? 'bg-primary/15 text-slate-100 ring-1 ring-inset ring-primary/30'
                            : 'bg-border text-slate-200';
                        $badgeIcon = $isPlayer ? 'fa-solid fa-user' : 'fa-solid fa-users';
                        $badgeLabel = $isPlayer ? __('Player') : __('NPC');
                    @endphp

                    <p class="mt-1 text-xs text-slate-300">
                        <span class="inline-flex items-center gap-2 rounded-full px-2 py-0.5 text-[11px] font-medium {{ $badgeStyles }}">
                            <i class="{{ $badgeIcon }} text-[11px]"></i>
                            {{ $badgeLabel }}
                        </span>
                    </p>
                @endif

                @if ($entity->description)
                    <p class="mt-1 text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($entity->description, 110) }}</p>
                @endif
            </div>

            <a
                href="{{ $actionHref }}"
                class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-surface/40 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface"
            >
                {{ $actionLabel }}
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

        <div class="mt-5 flex items-center justify-between gap-3">
            <x-secondary-button type="button" x-data @click="$dispatch('open-modal', '{{ $modalName }}')">
                <i class="fa-solid fa-link text-xs"></i>
                {{ __('Add to Campaign') }}
            </x-secondary-button>

            <form method="POST" action="{{ route($type.'.destroy', $entity) }}" onsubmit="return confirm('{{ __('Delete this item?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-rose-200 transition hover:bg-rose-500/10">
                    {{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>

    <x-modal name="{{ $modalName }}" :show="false" maxWidth="md">
        <div class="p-6">
            <h2 class="text-sm font-semibold text-slate-100">{{ __('Add to Campaign') }}</h2>
            <p class="mt-1 text-xs text-slate-400">{{ __('Select a campaign to attach this entity.') }}</p>

            <form class="mt-4 space-y-4" method="POST" action="{{ route($type.'.attach-to-campaign', $entity) }}">
                @csrf

                <div>
                    <x-input-label for="campaign_id_{{ $type }}_{{ $entity->id }}" :value="__('Campaign')" />
                    <select
                        id="campaign_id_{{ $type }}_{{ $entity->id }}"
                        name="campaign_id"
                        class="ls-focus mt-1 block w-full rounded-xl border-border bg-app-bg/60 text-slate-100 shadow-sm focus:border-interactive focus:ring-interactive/50"
                        required
                    >
                        <option value="" disabled selected>{{ __('Select...') }}</option>
                        @foreach ($campaigns as $campaign)
                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-secondary-button type="button" x-data @click="$dispatch('close-modal', '{{ $modalName }}')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Attach') }}</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</x-card>
