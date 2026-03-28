<?php

namespace App\Services;

use App\Models\NPC;
use App\Models\User;

class NPCService
{
    /**
     * @param array{location_id?:int|null,name:string,title?:string|null,description?:string|null,status?:string|null,image_path?:string|null} $data
     */
    public function create(User $owner, array $data): NPC
    {
        return NPC::create([
            'user_id' => $owner->id,
            'location_id' => $data['location_id'] ?? null,
            'name' => $data['name'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? null,
            'image_path' => $data['image_path'] ?? null,
        ]);
    }

    /**
     * @param array{location_id?:int|null,name:string,title?:string|null,description?:string|null,status?:string|null,image_path?:string|null} $data
     */
    public function update(NPC $npc, array $data): NPC
    {
        $npc->location_id = $data['location_id'] ?? $npc->location_id;
        $npc->name = $data['name'];
        $npc->title = $data['title'] ?? null;
        $npc->description = $data['description'] ?? null;
        $npc->status = $data['status'] ?? null;

        if (array_key_exists('image_path', $data)) {
            $npc->image_path = $data['image_path'];
        }

        $npc->save();

        return $npc;
    }

    /**
     * @param array<int, array{key?: mixed, value?: mixed}> $attributes
     */
    public function syncAttributes(NPC $npc, array $attributes): void
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

        $npc->npcAttributes()->delete();

        if ($cleanAttributes->isEmpty()) {
            return;
        }

        $npc->npcAttributes()->createMany($cleanAttributes->all());
    }

    /**
     * @param array<int, array{related_npc_id?: mixed, type?: mixed, description?: mixed}> $relationships
     */
    public function syncRelationships(NPC $npc, array $relationships): void
    {
        $cleanRelationships = collect($relationships)
            ->map(function (array $relationship) {
                $relatedId = $relationship['related_npc_id'] ?? null;
                $type = isset($relationship['type']) ? trim((string) $relationship['type']) : '';
                $description = isset($relationship['description']) ? trim((string) $relationship['description']) : '';

                return [
                    'related_npc_id' => is_numeric($relatedId) ? (int) $relatedId : null,
                    'type' => $type === '' ? null : strtolower($type),
                    'description' => $description === '' ? null : $description,
                ];
            })
            ->filter(fn (array $relationship) => $relationship['related_npc_id'] !== null && $relationship['type'] !== null)
            ->filter(fn (array $relationship) => $relationship['related_npc_id'] !== $npc->id)
            ->values();

        $npc->outgoingRelationships()->delete();

        if ($cleanRelationships->isEmpty()) {
            return;
        }

        $npc->outgoingRelationships()->createMany($cleanRelationships->all());
    }
}
