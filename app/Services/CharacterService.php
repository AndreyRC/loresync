<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\Ability;
use App\Models\Item;
use App\Models\User;

class CharacterService
{
    /**
     * @param array{location_id?:int|null,name:string,title?:string|null,description?:string|null,status?:string|null,type:string,image_path?:string|null} $data
     */
    public function create(User $owner, array $data): Character
    {
        return Character::create([
            'user_id' => $owner->id,
            'location_id' => $data['location_id'] ?? null,
            'name' => $data['name'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? null,
            'type' => $data['type'],
            'image_path' => $data['image_path'] ?? null,
        ]);
    }

    /**
     * @param array{location_id?:int|null,name:string,title?:string|null,description?:string|null,status?:string|null,type:string,image_path?:string|null} $data
     */
    public function update(Character $character, array $data): Character
    {
        $character->location_id = $data['location_id'] ?? $character->location_id;
        $character->name = $data['name'];
        $character->title = $data['title'] ?? null;
        $character->description = $data['description'] ?? null;
        $character->status = $data['status'] ?? null;
        $character->type = $data['type'];

        if (array_key_exists('image_path', $data)) {
            $character->image_path = $data['image_path'];
        }

        $character->save();

        return $character;
    }

    /**
     * @param array<int, array{key?: mixed, value?: mixed}> $attributes
     */
    public function syncAttributes(Character $character, array $attributes): void
    {
        $cleanAttributes = collect($attributes)
            ->map(function (array $attribute) {
                $key = isset($attribute['key']) ? trim((string) $attribute['key']) : '';
                $value = isset($attribute['value']) ? trim((string) $attribute['value']) : '';

                return [
                    'key' => $key,
                    'value' => $value === '' ? null : $value,
                ];
            })
            ->filter(fn (array $attribute) => $attribute['key'] !== '' && ($attribute['value'] !== null))
            ->values();

        $character->characterAttributes()->delete();

        if ($cleanAttributes->isEmpty()) {
            return;
        }

        $character->characterAttributes()->createMany($cleanAttributes->all());
    }

    /**
     * @param array<int, array{related_character_id?: mixed, type?: mixed, description?: mixed}> $relationships
     */
    public function syncRelationships(Character $character, array $relationships): void
    {
        $cleanRelationships = collect($relationships)
            ->map(function (array $relationship) {
                $relatedId = $relationship['related_character_id'] ?? null;
                $type = isset($relationship['type']) ? trim((string) $relationship['type']) : '';
                $description = isset($relationship['description']) ? trim((string) $relationship['description']) : '';

                return [
                    'related_character_id' => is_numeric($relatedId) ? (int) $relatedId : null,
                    'type' => $type === '' ? null : strtolower($type),
                    'description' => $description === '' ? null : $description,
                ];
            })
            ->filter(fn (array $relationship) => $relationship['related_character_id'] !== null && $relationship['type'] !== null)
            ->filter(fn (array $relationship) => $relationship['related_character_id'] !== $character->id)
            ->values();

        $character->outgoingRelationships()->delete();

        if ($cleanRelationships->isEmpty()) {
            return;
        }

        $character->outgoingRelationships()->createMany($cleanRelationships->all());
    }

    public function convertToPlayer(Character $character, User $player, Campaign $campaign): Character
    {
        $character->type = Character::TYPE_PLAYER;
        $character->save();

        $character->campaigns()->syncWithoutDetaching([$campaign->id]);

        $character->playerProfile()->updateOrCreate([
            'character_id' => $character->id,
        ], [
            'player_user_id' => $player->id,
            'campaign_id' => $campaign->id,
        ]);

        return $character;
    }

    public function addOrUpdateInventoryItem(Character $character, Item $item, int $quantity = 1, ?string $notes = null): void
    {
        $quantity = max(1, $quantity);

        $character->items()->syncWithoutDetaching([
            $item->id => [
                'quantity' => $quantity,
                'notes' => $notes,
            ],
        ]);
    }

    public function removeInventoryItem(Character $character, Item $item): void
    {
        $character->items()->detach($item->id);
    }

    public function addOrUpdateAbility(Character $character, Ability $ability, ?string $notes = null): void
    {
        $character->abilities()->syncWithoutDetaching([
            $ability->id => [
                'notes' => $notes,
            ],
        ]);
    }

    public function removeAbility(Character $character, Ability $ability): void
    {
        $character->abilities()->detach($ability->id);
    }
}
