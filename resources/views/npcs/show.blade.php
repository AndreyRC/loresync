<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-users text-sm text-interactive"></i>
                <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('NPC') }}</h1>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('npcs.index') }}"
                    class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70"
                >
                    {{ __('Back') }}
                </a>

                <a
                    href="{{ route('npcs.edit', $npc) }}"
                    class="ls-focus inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-primary-hover"
                >
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $status = $npc->status;

        $statusStyles = match ($status) {
            'alive' => 'bg-emerald-500/15 text-emerald-200 ring-1 ring-inset ring-emerald-500/30',
            'dead' => 'bg-rose-500/15 text-rose-200 ring-1 ring-inset ring-rose-500/30',
            'missing' => 'bg-amber-500/15 text-amber-200 ring-1 ring-inset ring-amber-500/30',
            default => 'bg-slate-500/15 text-slate-200 ring-1 ring-inset ring-slate-500/30',
        };

        $statusLabel = $status ? \Illuminate\Support\Str::headline($status) : __('Unknown');

        $relationships = $npc->outgoingRelationships ?? collect();
        $relationshipsGrouped = $relationships->groupBy(function ($relationship) {
            $type = strtolower((string) $relationship->type);

            return match ($type) {
                'ally', 'allies' => 'allies',
                'enemy', 'enemies' => 'enemies',
                default => 'others',
            };
        });

        $attributes = $npc->npcAttributes ?? collect();
        $tags = $npc->tags ?? collect();
    @endphp

    <x-card class="p-0 overflow-hidden">
        <div class="grid grid-cols-1 gap-0 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <div class="h-64 w-full bg-surface/60">
                    @if ($npc->image_path)
                        <img
                            src="{{ asset('storage/'.$npc->image_path) }}"
                            alt=""
                            class="h-64 w-full object-cover"
                        />
                    @else
                        <div class="flex h-64 w-full items-center justify-center bg-surface/40">
                            <i class="fa-solid fa-user text-4xl text-slate-400"></i>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-6 lg:col-span-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-100">{{ $npc->name }}</h2>
                        @if ($npc->title)
                            <p class="mt-1 text-sm text-slate-300">{{ $npc->title }}</p>
                        @endif

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-medium {{ $statusStyles }}">
                                <i class="fa-solid fa-heart-pulse text-[11px]"></i>
                                {{ $statusLabel }}
                            </span>

                            @foreach ($tags as $tag)
                                <span class="inline-flex items-center rounded-full bg-primary/15 px-3 py-1 text-[11px] font-medium text-slate-100 ring-1 ring-inset ring-primary/30">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if ($npc->description)
                    <div class="mt-5">
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-slate-300">
                            <i class="fa-solid fa-scroll mr-2 text-xs"></i>
                            {{ __('Description') }}
                        </h3>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $npc->description }}</p>
                    </div>
                @endif

                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-slate-300">
                            <i class="fa-solid fa-list mr-2 text-xs"></i>
                            {{ __('Attributes') }}
                        </h3>

                        @if ($attributes->count())
                            <div class="mt-3 space-y-2 text-sm">
                                @foreach ($attributes as $attribute)
                                    <div class="flex items-baseline justify-between gap-3">
                                        <span class="text-slate-200">{{ \Illuminate\Support\Str::headline($attribute->key) }}</span>
                                        <span class="text-slate-400">{{ $attribute->value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-3 text-sm text-slate-400">{{ __('No attributes yet.') }}</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-slate-300">
                            <i class="fa-solid fa-share-nodes mr-2 text-xs"></i>
                            {{ __('Relationships') }}
                        </h3>

                        @if ($relationships->count())
                            <div class="mt-3 space-y-4">
                                @foreach (['allies' => __('Allies'), 'enemies' => __('Enemies'), 'others' => __('Others')] as $groupKey => $groupLabel)
                                    @php
                                        $groupRels = $relationshipsGrouped->get($groupKey, collect());
                                    @endphp

                                    @if ($groupRels->count())
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ $groupLabel }}</p>
                                            <div class="mt-2 space-y-2">
                                                @foreach ($groupRels as $relationship)
                                                    <div class="text-sm text-slate-200">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <span>{{ $npc->name }}</span>
                                                            <span class="text-slate-500">→</span>
                                                            <span class="inline-flex items-center rounded-full bg-border px-2 py-0.5 text-[11px] font-medium text-slate-200">
                                                                {{ \Illuminate\Support\Str::headline($relationship->type) }}
                                                            </span>
                                                            <span class="text-slate-500">→</span>
                                                            <span class="text-slate-200">{{ $relationship->relatedNpc?->name ?? __('Unknown') }}</span>
                                                        </div>

                                                        @if ($relationship->description)
                                                            <p class="mt-1 text-xs text-slate-400">{{ $relationship->description }}</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="mt-3 text-sm text-slate-400">{{ __('No relationships yet.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-card>
</x-app-layout>
