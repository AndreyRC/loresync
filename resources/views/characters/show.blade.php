<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row gap-3 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-user-group text-sm text-interactive"></i>
                <h1 class="text-sm font-semibold tracking-wide text-slate-100">{{ __('Character') }}</h1>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('characters.index') }}"
                    class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70"
                >
                    {{ __('Back') }}
                </a>

                <a
                    href="{{ route('characters.edit', $character) }}"
                    class="ls-focus inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-primary-hover"
                >
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $status = $character->status;

        $statusStyles = match ($status) {
            'alive' => 'bg-emerald-500/15 text-emerald-200 ring-1 ring-inset ring-emerald-500/30',
            'dead' => 'bg-rose-500/15 text-rose-200 ring-1 ring-inset ring-rose-500/30',
            'missing' => 'bg-amber-500/15 text-amber-200 ring-1 ring-inset ring-amber-500/30',
            default => 'bg-slate-500/15 text-slate-200 ring-1 ring-inset ring-slate-500/30',
        };

        $statusLabel = $status ? \Illuminate\Support\Str::headline($status) : __('Unknown');

        $typeStyles = $character->type === 'player'
            ? 'bg-primary/15 text-slate-100 ring-1 ring-inset ring-primary/30'
            : 'bg-border text-slate-200';

        $typeIcon = $character->type === 'player'
            ? 'fa-solid fa-user'
            : 'fa-solid fa-users';

        $typeLabel = $character->type === 'player' ? __('Player') : __('NPC');

        $relationships = $character->outgoingRelationships ?? collect();
        $relationshipsGrouped = $relationships->groupBy(function ($relationship) {
            $type = strtolower((string) $relationship->type);

            return match ($type) {
                'ally', 'allies' => 'allies',
                'enemy', 'enemies' => 'enemies',
                default => 'others',
            };
        });

        $attributes = $character->characterAttributes ?? collect();
        $tags = $character->tags ?? collect();
        $inventory = $character->items ?? collect();
        $abilities = $character->abilities ?? collect();

        $inventoryModal = 'add-item-character-'.$character->id;
        $abilityModal = 'add-ability-character-'.$character->id;
    @endphp

    <x-card class="p-0 overflow-hidden">
        <div class="grid grid-cols-1 gap-0 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <div class="h-64 w-full bg-surface/60">
                    @if ($character->image_path)
                        <img
                            src="{{ asset('storage/'.$character->image_path) }}"
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
                        <h2 class="text-lg font-semibold text-slate-100">{{ $character->name }}</h2>
                        @if ($character->title)
                            <p class="mt-1 text-sm text-slate-300">{{ $character->title }}</p>
                        @endif

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-medium {{ $typeStyles }}">
                                <i class="{{ $typeIcon }} text-[11px]"></i>
                                {{ $typeLabel }}
                            </span>

                            @if ($character->type !== 'player')
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-medium {{ $statusStyles }}">
                                    <i class="fa-solid fa-heart-pulse text-[11px]"></i>
                                    {{ $statusLabel }}
                                </span>
                            @endif

                            @foreach ($tags as $tag)
                                <span class="inline-flex items-center rounded-full bg-primary/15 px-3 py-1 text-[11px] font-medium text-slate-100 ring-1 ring-inset ring-primary/30">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>

                        @if ($character->type === 'player' && $character->playerProfile)
                            <div class="mt-3 text-xs text-slate-400">
                                <p>
                                    <i class="fa-solid fa-user mr-2"></i>
                                    {{ __('Player:') }}
                                    <span class="text-slate-200">{{ $character->playerProfile->player?->name ?? __('Unknown') }}</span>
                                </p>
                                <p class="mt-1">
                                    <i class="fa-solid fa-scroll mr-2"></i>
                                    {{ __('Campaign:') }}
                                    <span class="text-slate-200">{{ $character->playerProfile->campaign?->name ?? __('Unknown') }}</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($character->description)
                    <div class="mt-5">
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-slate-300">
                            <i class="fa-solid fa-scroll mr-2 text-xs"></i>
                            {{ __('Description') }}
                        </h3>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $character->description }}</p>
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
                                                            <span>{{ $character->name }}</span>
                                                            <span class="text-slate-500">→</span>
                                                            <span class="inline-flex items-center rounded-full bg-border px-2 py-0.5 text-[11px] font-medium text-slate-200">
                                                                {{ \Illuminate\Support\Str::headline($relationship->type) }}
                                                            </span>
                                                            <span class="text-slate-500">→</span>
                                                            <span class="text-slate-200">{{ $relationship->relatedCharacter?->name ?? __('Unknown') }}</span>
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

    <x-card class="mt-6">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-slate-100">
                <i class="fa-solid fa-bolt mr-2 text-xs"></i>
                {{ __('Abilities') }}
            </h3>

            <x-secondary-button type="button" x-data @click="$dispatch('open-modal', '{{ $abilityModal }}')">
                <i class="fa-solid fa-plus text-xs"></i>
                {{ __('Add Ability') }}
            </x-secondary-button>
        </div>

        @if ($abilities->count())
            <div class="mt-4 space-y-3">
                @foreach ($abilities as $ability)
                    @php
                        $abilityAttributes = $ability->abilityAttributes ?? collect();
                        $hasDetails = (bool) $ability->description || $abilityAttributes->count();
                    @endphp

                    <div class="rounded-xl border border-border bg-surface/40 p-4" @if ($hasDetails) x-data="{ open: false }" @endif>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 overflow-hidden rounded-lg bg-surface/60">
                                    @if ($ability->image_path)
                                        <img src="{{ asset('storage/'.$ability->image_path) }}" alt="" class="h-10 w-10 object-cover" />
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center bg-surface/40">
                                            <i class="fa-solid fa-bolt text-slate-400"></i>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold text-slate-100">{{ $ability->name }}</p>
                                        @if ($ability->type)
                                            <span class="inline-flex items-center gap-2 rounded-full bg-primary/15 px-2 py-0.5 text-[11px] font-medium text-slate-100 ring-1 ring-inset ring-primary/30">
                                                <i class="fa-solid fa-tag text-[11px]"></i>
                                                {{ $ability->type }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($ability->pivot?->notes)
                                        <p class="mt-1 text-xs text-slate-400">{{ $ability->pivot->notes }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($hasDetails)
                                    <button
                                        type="button"
                                        class="ls-focus inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70"
                                        @click="open = !open"
                                    >
                                        <span x-text="open ? '{{ __('Hide details') }}' : '{{ __('View details') }}'"></span>
                                        <i class="fa-solid fa-chevron-down text-[10px] transition" :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                @endif

                                <a
                                    href="{{ route('abilities.edit', $ability) }}"
                                    class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70"
                                >
                                    {{ __('Edit') }}
                                </a>

                                <form method="POST" action="{{ route('characters.abilities.destroy', [$character, $ability]) }}" onsubmit="return confirm('{{ __('Remove this ability from the character?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-rose-200 transition hover:bg-rose-500/10">
                                        {{ __('Remove') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if ($hasDetails)
                            <div class="mt-4 border-t border-border/70 pt-4" x-show="open" x-collapse>
                                @if ($ability->description)
                                    <p class="text-sm text-slate-200 whitespace-pre-line">{{ $ability->description }}</p>
                                @endif

                                @if ($abilityAttributes->count())
                                    <div class="mt-3 space-y-2 text-sm">
                                        @foreach ($abilityAttributes as $attribute)
                                            <div class="flex items-baseline justify-between gap-3">
                                                <span class="text-slate-200">{{ \Illuminate\Support\Str::headline($attribute->key) }}</span>
                                                <span class="text-slate-400">{{ $attribute->value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-3 text-sm text-slate-400">{{ __('No abilities yet.') }}</p>
        @endif
    </x-card>

    <x-card class="mt-6">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-slate-100">
                <i class="fa-solid fa-box-open mr-2 text-xs"></i>
                {{ __('Inventory') }}
            </h3>

            <x-secondary-button type="button" x-data @click="$dispatch('open-modal', '{{ $inventoryModal }}')">
                <i class="fa-solid fa-plus text-xs"></i>
                {{ __('Add Item') }}
            </x-secondary-button>
        </div>

        @if ($inventory->count())
            <div class="mt-4 space-y-3">
                @foreach ($inventory as $item)
                    @php
                        $itemAttributes = $item->itemAttributes ?? collect();
                        $hasDetails = (bool) $item->description || $itemAttributes->count();
                    @endphp

                    <div class="rounded-xl border border-border bg-surface/40 p-4" @if ($hasDetails) x-data="{ open: false }" @endif>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                            <div class="h-10 w-10 overflow-hidden rounded-lg bg-surface/60">
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/'.$item->image_path) }}" alt="" class="h-10 w-10 object-cover" />
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center bg-surface/40">
                                        <i class="fa-solid fa-box text-slate-400"></i>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-slate-100">{{ $item->name }}</p>
                                <p class="text-xs text-slate-400">{{ __('Quantity: :qty', ['qty' => $item->pivot->quantity ?? 1]) }}</p>
                                @if ($item->pivot->notes)
                                    <p class="mt-1 text-xs text-slate-400">{{ $item->pivot->notes }}</p>
                                @endif
                            </div>
                        </div>

                            <div class="flex items-center gap-2">
                                @if ($hasDetails)
                                    <button
                                        type="button"
                                        class="ls-focus inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-surface/70"
                                        @click="open = !open"
                                    >
                                        <span x-text="open ? '{{ __('Hide details') }}' : '{{ __('View details') }}'"></span>
                                        <i class="fa-solid fa-chevron-down text-[10px] transition" :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('characters.inventory.destroy', [$character, $item]) }}" onsubmit="return confirm('{{ __('Remove this item from inventory?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ls-focus inline-flex items-center justify-center rounded-xl border border-border bg-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-rose-200 transition hover:bg-rose-500/10">
                                        {{ __('Remove') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if ($hasDetails)
                            <div class="mt-4 border-t border-border/70 pt-4" x-show="open" x-collapse>
                                @if ($item->description)
                                    <p class="text-sm text-slate-200 whitespace-pre-line">{{ $item->description }}</p>
                                @endif

                                @if ($itemAttributes->count())
                                    <div class="mt-3 space-y-2 text-sm">
                                        @foreach ($itemAttributes as $attribute)
                                            <div class="flex items-baseline justify-between gap-3">
                                                <span class="text-slate-200">{{ \Illuminate\Support\Str::headline($attribute->key) }}</span>
                                                <span class="text-slate-400">{{ $attribute->value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-3 text-sm text-slate-400">{{ __('No items yet.') }}</p>
        @endif
    </x-card>

    <x-modal name="{{ $inventoryModal }}" :show="false" maxWidth="md">
        <div class="p-6">
            <h2 class="text-sm font-semibold text-slate-100">{{ __('Add Item') }}</h2>
            <p class="mt-1 text-xs text-slate-400">{{ __('Select an item to add to this character\'s inventory.') }}</p>

            <form class="mt-4 space-y-4" method="POST" action="{{ route('characters.inventory.store', $character) }}">
                @csrf

                <x-select
                    id="inventory_item_id_{{ $character->id }}"
                    name="item_id"
                    :label="__('Item')"
                    icon="tag"
                    required
                >
                    <option value="" disabled selected>{{ __('Select...') }}</option>
                    @foreach ($availableItems as $availableItem)
                        <option value="{{ $availableItem->id }}">{{ $availableItem->name }}</option>
                    @endforeach
                </x-select>

                <x-input
                    name="quantity"
                    type="number"
                    min="1"
                    :label="__('Quantity')"
                    icon="tag"
                    :value="old('quantity', 1)"
                />

                <x-textarea
                    name="notes"
                    rows="3"
                    :label="__('Notes')"
                    icon="scroll"
                    :value="old('notes')"
                />

                <div class="flex items-center justify-end gap-3">
                    <x-secondary-button type="button" x-data @click="$dispatch('close-modal', '{{ $inventoryModal }}')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Add') }}</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="{{ $abilityModal }}" :show="false" maxWidth="md">
        <div class="p-6">
            <h2 class="text-sm font-semibold text-slate-100">{{ __('Add Ability') }}</h2>
            <p class="mt-1 text-xs text-slate-400">{{ __('Select an ability to attach to this character.') }}</p>

            <form class="mt-4 space-y-4" method="POST" action="{{ route('characters.abilities.store', $character) }}">
                @csrf

                <x-select
                    id="ability_id_{{ $character->id }}"
                    name="ability_id"
                    :label="__('Ability')"
                    icon="tag"
                    required
                >
                    <option value="" disabled selected>{{ __('Select...') }}</option>
                    @foreach ($availableAbilities as $availableAbility)
                        <option value="{{ $availableAbility->id }}">
                            {{ $availableAbility->name }}@if ($availableAbility->type) — {{ $availableAbility->type }}@endif
                        </option>
                    @endforeach
                </x-select>

                <x-textarea
                    name="notes"
                    rows="3"
                    :label="__('Notes')"
                    icon="scroll"
                    :value="old('notes')"
                />

                <div class="flex items-center justify-end gap-3">
                    <x-secondary-button type="button" x-data @click="$dispatch('close-modal', '{{ $abilityModal }}')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Add') }}</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
