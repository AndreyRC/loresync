<?php

namespace App\Services;

use App\Models\Item;

class ItemService
{
    /**
     * @param array<int, array{key?: mixed, value?: mixed}> $attributes
     */
    public function syncAttributes(Item $item, array $attributes): void
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

        $item->itemAttributes()->delete();

        if ($cleanAttributes->isEmpty()) {
            return;
        }

        $item->itemAttributes()->createMany($cleanAttributes->all());
    }
}
