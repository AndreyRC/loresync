<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['user_id', 'location_id', 'name', 'title', 'description', 'status', 'type', 'image_path'])]
class Character extends Model
{
    public const TYPE_NPC = 'npc';

    public const TYPE_PLAYER = 'player';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_character');
    }

    public function characterAttributes(): HasMany
    {
        return $this->hasMany(CharacterAttribute::class);
    }

    public function outgoingRelationships(): HasMany
    {
        return $this->hasMany(CharacterRelationship::class, 'character_id');
    }

    public function incomingRelationships(): HasMany
    {
        return $this->hasMany(CharacterRelationship::class, 'related_character_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'character_item')
            ->withPivot(['quantity', 'notes'])
            ->withTimestamps();
    }

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class, 'ability_character')
            ->withPivot(['notes'])
            ->withTimestamps();
    }

    public function playerProfile(): HasOne
    {
        return $this->hasOne(PlayerProfile::class);
    }
}
