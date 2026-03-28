<?php

namespace App\Services;

use App\Models\Ability;
use App\Models\User;

class AbilityService
{
    /**
     * @param array{name:string,description?:string|null,type?:string|null,image_path?:string|null} $data
     */
    public function create(User $owner, array $data): Ability
    {
        return Ability::create([
            'user_id' => $owner->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? null,
            'image_path' => $data['image_path'] ?? null,
        ]);
    }

    /**
     * @param array{name:string,description?:string|null,type?:string|null,image_path?:string|null} $data
     */
    public function update(Ability $ability, array $data): Ability
    {
        $ability->name = $data['name'];
        $ability->description = $data['description'] ?? null;
        $ability->type = $data['type'] ?? null;

        if (array_key_exists('image_path', $data)) {
            $ability->image_path = $data['image_path'];
        }

        $ability->save();

        return $ability;
    }

    /**
     * @param array<int, array{key?: mixed, value?: mixed}> $attributes
     */
    public function syncAttributes(Ability $ability, array $attributes): void
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

        $ability->abilityAttributes()->delete();

        if ($cleanAttributes->isEmpty()) {
            return;
        }

        $ability->abilityAttributes()->createMany($cleanAttributes->all());
    }
}
