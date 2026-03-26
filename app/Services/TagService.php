<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TagService
{
    /**
     * @param Model $taggable
     * @param array<int, string> $tagNames
     */
    public function syncTags(Model $taggable, User $user, array $tagNames): void
    {
        $cleanNames = collect($tagNames)
            ->map(fn ($name) => trim($name))
            ->filter(fn ($name) => $name !== '')
            ->unique()
            ->values();

        $tagIds = $cleanNames
            ->map(function (string $name) use ($user) {
                return Tag::query()->firstOrCreate([
                    'user_id' => $user->id,
                    'name' => $name,
                ])->id;
            })
            ->all();

        // @phpstan-ignore-next-line
        $taggable->tags()->sync($tagIds);
    }
}
