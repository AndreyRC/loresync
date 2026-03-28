<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['user_id', 'location_id', 'name', 'title', 'description', 'status', 'image_path'])]
class NPC extends Model
{
    protected $table = 'npcs';

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
        return $this->belongsToMany(Campaign::class, 'campaign_npc', 'entity_id', 'campaign_id');
    }

    public function npcAttributes(): HasMany
    {
        return $this->hasMany(NPCAttribute::class);
    }

    public function outgoingRelationships(): HasMany
    {
        return $this->hasMany(NPCRelationship::class, 'npc_id');
    }

    public function incomingRelationships(): HasMany
    {
        return $this->hasMany(NPCRelationship::class, 'related_npc_id');
    }
}
