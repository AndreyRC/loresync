@props([
    'type',
    'entity' => null,
    'tags' => [],
    'availableTags' => [],
    'availableNpcs' => [],
    'availableCharacters' => [],
    'availablePlayers' => [],
    'availableCampaigns' => [],
    'title' => null,
    'submitLabel' => null,
])

@php
    $isEditing = $entity !== null;
    $action = $isEditing ? route($type.'.update', $entity) : route($type.'.store');
    $method = $isEditing ? 'PUT' : 'POST';

    $isItemForm = $type === 'items';
    $isNpcForm = $type === 'npcs';
    $isCharacterForm = $type === 'characters';
    $isAbilityForm = $type === 'abilities';

    $relationshipIdKey = $isCharacterForm ? 'related_character_id' : 'related_npc_id';
    $relationshipEntities = $isCharacterForm ? $availableCharacters : $availableNpcs;

    $resolvedTitle = $title ?? ($isEditing ? __('Edit') : __('Create'));
    $resolvedSubmitLabel = $submitLabel ?? ($isEditing ? __('Save') : __('Create'));

    $initialTags = collect($tags)
        ->map(fn ($t) => is_string($t) ? $t : '')
        ->filter(fn ($t) => $t !== '')
        ->values()
        ->all();

    $initialAttributes = old('attributes');

    if (!is_array($initialAttributes)) {
        $initialAttributes = [];

        if ($isItemForm) {
            $initialAttributes = $entity?->itemAttributes
                ?->map(fn ($a) => ['key' => $a->key, 'value' => $a->value])
                ->all() ?? [];
        }
        if ($isNpcForm) {
            $initialAttributes = $entity?->npcAttributes
                ?->map(fn ($a) => ['key' => $a->key, 'value' => $a->value])
                ->all() ?? [];
        }
        if ($isCharacterForm) {
            $initialAttributes = $entity?->characterAttributes
                ?->map(fn ($a) => ['key' => $a->key, 'value' => $a->value])
                ->all() ?? [];
        }
        if ($isAbilityForm) {
            $initialAttributes = $entity?->abilityAttributes
                ?->map(fn ($a) => ['key' => $a->key, 'value' => $a->value])
                ->all() ?? [];
        }
    }

    $initialAttributes = collect($initialAttributes)
        ->map(function ($attribute) {
            if (!is_array($attribute)) {
                return ['key' => '', 'value' => ''];
            }

            return [
                'key' => isset($attribute['key']) ? (string) $attribute['key'] : '',
                'value' => isset($attribute['value']) ? (string) $attribute['value'] : '',
            ];
        })
        ->values()
        ->all();

    $initialRelationships = old('relationships');

    if (!is_array($initialRelationships)) {
        $initialRelationships = ($isNpcForm || $isCharacterForm)
            ? ($entity?->outgoingRelationships?->map(fn ($r) => [
                'related_id' => $isCharacterForm ? $r->related_character_id : $r->related_npc_id,
                'type' => $r->type,
                'description' => $r->description,
            ])->all() ?? [])
            : [];
    }

    $initialRelationships = collect($initialRelationships)
        ->map(function ($relationship) {
            if (!is_array($relationship)) {
                return ['related_id' => '', 'type' => 'ally', 'description' => ''];
            }

            return [
                'related_id' => isset($relationship['related_character_id'])
                    ? (string) $relationship['related_character_id']
                    : (isset($relationship['related_npc_id']) ? (string) $relationship['related_npc_id'] : (isset($relationship['related_id']) ? (string) $relationship['related_id'] : '')),
                'type' => isset($relationship['type']) && (string) $relationship['type'] !== '' ? (string) $relationship['type'] : 'ally',
                'description' => isset($relationship['description']) ? (string) $relationship['description'] : '',
            ];
        })
        ->values()
        ->all();

    $initialCharacterType = old('type', $entity?->type ?? 'npc');
    $initialPlayerUserId = old('player_user_id', $entity?->playerProfile?->player_user_id ?? '');
    $initialCampaignId = old('campaign_id', $entity?->playerProfile?->campaign_id ?? '');
@endphp

<x-card>
    @if ($resolvedTitle)
        <h2 class="text-sm font-semibold text-slate-100">{{ $resolvedTitle }}</h2>
        <div class="mt-4 border-t border-border/70"></div>
    @endif

    <form
        class="mt-4 space-y-5"
        method="POST"
        action="{{ $action }}"
        enctype="multipart/form-data"
        x-data="{
            attributes: @js($initialAttributes),
            relationships: @js($initialRelationships),
            characterType: @js($initialCharacterType),
            playerUserId: @js((string) $initialPlayerUserId),
            campaignId: @js((string) $initialCampaignId),
            addAttribute() {
                this.attributes.push({ key: '', value: '' });
            },
            removeAttribute(index) {
                this.attributes.splice(index, 1);
            },
            ensureAtLeastOne() {
                if (this.attributes.length === 0) this.addAttribute();
            },
            addRelationship() {
                this.relationships.push({ related_id: '', type: 'ally', description: '' });
            },
            removeRelationship(index) {
                this.relationships.splice(index, 1);
            },
        }"
        x-init="if ({{ ($isItemForm || $isAbilityForm) ? 'true' : 'false' }}) ensureAtLeastOne()"
    >
        @csrf
        @method($method)

        <x-input
            name="name"
            :label="__('Name')"
            icon="user"
            :value="old('name', $entity?->name)"
            required
            autofocus
        />

        @if ($isNpcForm || $isCharacterForm)
            <x-input
                name="title"
                :label="__('Title')"
                icon="scroll"
                :value="old('title', $entity?->title)"
                placeholder="{{ __('e.g. Captain of the Guard') }}"
            />
        @endif

        @if ($isCharacterForm)
            <x-select
                name="type"
                :label="__('Type')"
                icon="user"
                x-model="characterType"
                required
            >
                <option value="npc">{{ __('NPC') }}</option>
                <option value="player">{{ __('Player') }}</option>
            </x-select>
        @endif

        <x-textarea
            name="description"
            :label="__('Description')"
            icon="scroll"
            rows="5"
            :value="old('description', $entity?->description)"
        />

        @if ($isNpcForm || $isCharacterForm)
            @php
                $selectedStatus = old('status', $entity?->status);
            @endphp

            <div @if ($isCharacterForm) x-show="characterType === 'npc'" @endif>
                <x-select
                    name="status"
                    :label="__('Status')"
                >
                    <option value="">{{ __('Unknown') }}</option>
                    <option value="alive" @selected($selectedStatus === 'alive')>{{ __('Alive') }}</option>
                    <option value="dead" @selected($selectedStatus === 'dead')>{{ __('Dead') }}</option>
                    <option value="missing" @selected($selectedStatus === 'missing')>{{ __('Missing') }}</option>
                    <option value="unknown" @selected($selectedStatus === 'unknown')>{{ __('Unknown') }}</option>
                </x-select>
            </div>
        @endif

        @if ($isCharacterForm)
            <div class="space-y-3" x-show="characterType === 'player'">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <x-select
                            name="player_user_id"
                            :label="__('Player')"
                            icon="user"
                            x-model="playerUserId"
                        >
                            <option value="">{{ __('Select...') }}</option>
                            @foreach ($availablePlayers as $player)
                                <option value="{{ $player->id }}">{{ $player->name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('player_user_id')" />
                    </div>

                    <div>
                        <x-select
                            name="campaign_id"
                            :label="__('Campaign')"
                            icon="scroll"
                            x-model="campaignId"
                        >
                            <option value="">{{ __('Select...') }}</option>
                            @foreach ($availableCampaigns as $campaign)
                                <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('campaign_id')" />
                    </div>
                </div>
                <p class="text-xs text-slate-400">{{ __('Player characters require a Player and Campaign.') }}</p>
            </div>
        @endif

        @if ($isItemForm)
            <x-select
                name="type"
                :label="__('Type')"
                icon="tag"
            >
                @php
                    $selectedType = old('type', $entity?->type);
                @endphp
                <option value="">{{ __('Select...') }}</option>

                <option value="weapon" @selected($selectedType === 'weapon')>{{ __('Weapon') }}</option>
                <option value="armor" @selected($selectedType === 'armor')>{{ __('Armor') }}</option>
                <option value="consumable" @selected($selectedType === 'consumable')>{{ __('Consumable') }}</option>
                <option value="artifact" @selected($selectedType === 'artifact')>{{ __('Artifact') }}</option>
                <option value="misc" @selected($selectedType === 'misc')>{{ __('Misc') }}</option>
            </x-select>
        @endif

        @if ($isAbilityForm)
            <x-input
                name="type"
                :label="__('Type')"
                icon="tag"
                :value="old('type', $entity?->type)"
                placeholder="{{ __('e.g. spell') }}"
            />
        @endif

        <x-file-input
            name="image"
            :label="__('Image')"
            icon="image"
            accept="image/png,image/jpeg"
            :existing-url="$isEditing && $entity?->image_path ? asset('storage/'.$entity->image_path) : null"
        />

        <x-tag-input
            name="tags"
            :label="__('Tags')"
            :value="$initialTags"
            :available-tags="$availableTags"
        />

        @if ($isItemForm || $isNpcForm || $isCharacterForm || $isAbilityForm)
            <div class="space-y-2">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <x-input-label :value="__('Attributes')" />
                        <p class="mt-1 text-xs text-slate-400">
                            {{ $isItemForm
                                ? __('Add any custom fields you want (damage, rarity, weight, etc.).')
                                : ($isAbilityForm
                                    ? __('Add any custom fields you want (mana cost, damage, duration, etc.).')
                                    : __('Add any custom fields you want (strength, sanity, bonds, etc.).')
                                )
                            }}
                        </p>
                    </div>

                    <x-secondary-button type="button" @click="addAttribute()">
                        <i class="fa-solid fa-plus text-xs"></i>
                        {{ __('Add Attribute') }}
                    </x-secondary-button>
                </div>

                <div class="space-y-2">
                    <template x-for="(attribute, index) in attributes" :key="index">
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">
                            <div class="sm:col-span-5">
                                <input
                                    type="text"
                                    :name="`attributes[${index}][key]`"
                                    x-model="attribute.key"
                                    placeholder="{{ $isItemForm ? __('Damage') : ($isAbilityForm ? __('Mana Cost') : __('Strength')) }}"
                                    class="ls-focus block w-full rounded-xl border border-border bg-surface px-3 py-2 text-slate-200 placeholder:text-slate-500 shadow-sm transition hover:border-slate-600 focus:border-primary focus:ring-primary/30"
                                />
                            </div>

                            <div class="sm:col-span-6">
                                <input
                                    type="text"
                                    :name="`attributes[${index}][value]`"
                                    x-model="attribute.value"
                                    placeholder="{{ $isItemForm ? __('1d8') : ($isAbilityForm ? __('10') : __('14')) }}"
                                    class="ls-focus block w-full rounded-xl border border-border bg-surface px-3 py-2 text-slate-200 placeholder:text-slate-500 shadow-sm transition hover:border-slate-600 focus:border-primary focus:ring-primary/30"
                                />
                            </div>

                            <div class="sm:col-span-1 sm:flex sm:items-center">
                                <button
                                    type="button"
                                    class="ls-focus inline-flex w-full items-center justify-center rounded-xl border border-border bg-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-rose-200 transition hover:bg-rose-500/10"
                                    @click="removeAttribute(index)"
                                >
                                    <span class="sr-only">{{ __('Remove') }}</span>
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <x-input-error :messages="$errors->get('attributes')" />
                <x-input-error :messages="$errors->get('attributes.*.key')" />
                <x-input-error :messages="$errors->get('attributes.*.value')" />
            </div>
        @endif

        @if ($isNpcForm || $isCharacterForm)
            <div class="space-y-2">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <x-input-label :value="__('Relationships')" />
                        <p class="mt-1 text-xs text-slate-400">{{ __('Connect this NPC to others (ally, enemy, family, employer, etc.).') }}</p>
                    </div>

                    <x-secondary-button type="button" @click="addRelationship()">
                        <i class="fa-solid fa-plus text-xs"></i>
                        {{ __('Add Relationship') }}
                    </x-secondary-button>
                </div>

                <div class="space-y-2">
                    <template x-for="(relationship, index) in relationships" :key="index">
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">
                            <div class="sm:col-span-4">
                                <select
                                    :name="`relationships[${index}][{{ $relationshipIdKey }}]`"
                                    x-model="relationship.related_id"
                                    class="ls-focus block w-full appearance-none rounded-xl border border-border bg-surface px-3 py-2 text-slate-200 shadow-sm transition hover:border-slate-600 focus:border-primary focus:ring-primary/30"
                                    required
                                >
                                    <option value="" disabled>{{ $isCharacterForm ? __('Select Character...') : __('Select NPC...') }}</option>
                                    @foreach ($relationshipEntities as $entityOption)
                                        <option value="{{ $entityOption->id }}">{{ $entityOption->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-3">
                                <select
                                    :name="`relationships[${index}][type]`"
                                    x-model="relationship.type"
                                    class="ls-focus block w-full appearance-none rounded-xl border border-border bg-surface px-3 py-2 text-slate-200 shadow-sm transition hover:border-slate-600 focus:border-primary focus:ring-primary/30"
                                    required
                                >
                                    <option value="ally">{{ __('Ally') }}</option>
                                    <option value="enemy">{{ __('Enemy') }}</option>
                                    <option value="family">{{ __('Family') }}</option>
                                    <option value="employer">{{ __('Employer') }}</option>
                                    <option value="friend">{{ __('Friend') }}</option>
                                    <option value="rival">{{ __('Rival') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                </select>
                            </div>

                            <div class="sm:col-span-4">
                                <input
                                    type="text"
                                    :name="`relationships[${index}][description]`"
                                    x-model="relationship.description"
                                    placeholder="{{ __('Optional details') }}"
                                    class="ls-focus block w-full rounded-xl border border-border bg-surface px-3 py-2 text-slate-200 placeholder:text-slate-500 shadow-sm transition hover:border-slate-600 focus:border-primary focus:ring-primary/30"
                                />
                            </div>

                            <div class="sm:col-span-1 sm:flex sm:items-center">
                                <button
                                    type="button"
                                    class="ls-focus inline-flex w-full items-center justify-center rounded-xl border border-border bg-transparent px-3 py-2 text-xs font-semibold uppercase tracking-widest text-rose-200 transition hover:bg-rose-500/10"
                                    @click="removeRelationship(index)"
                                >
                                    <span class="sr-only">{{ __('Remove') }}</span>
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <x-input-error :messages="$errors->get('relationships')" />
                <x-input-error :messages="$errors->get('relationships.*.'.$relationshipIdKey)" />
                <x-input-error :messages="$errors->get('relationships.*.type')" />
                <x-input-error :messages="$errors->get('relationships.*.description')" />
            </div>
        @endif

        <div class="flex items-center justify-end gap-3">
            <x-secondary-button type="button" onclick="history.back()">{{ __('Cancel') }}</x-secondary-button>
            <x-primary-button>{{ $resolvedSubmitLabel }}</x-primary-button>
        </div>
    </form>
</x-card>
