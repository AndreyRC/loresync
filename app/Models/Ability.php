<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['user_id', 'name', 'description', 'type', 'image_path'])]
class Ability extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function abilityAttributes(): HasMany
    {
        return $this->hasMany(AbilityAttribute::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class, 'ability_character')
            ->withPivot(['notes'])
            ->withTimestamps();
    }
}
