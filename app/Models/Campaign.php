<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'description'])]
class Campaign extends Model
{
    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function npcs(): BelongsToMany
    {
        return $this->belongsToMany(NPC::class, 'campaign_npc', 'campaign_id', 'entity_id');
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class, 'campaign_character');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'campaign_location', 'campaign_id', 'entity_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'campaign_item', 'campaign_id', 'entity_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function maps(): HasMany
    {
        return $this->hasMany(Map::class);
    }
}
