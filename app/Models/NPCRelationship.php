<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['npc_id', 'related_npc_id', 'type', 'description'])]
class NPCRelationship extends Model
{
    protected $table = 'npc_relationships';

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NPC::class);
    }

    public function relatedNpc(): BelongsTo
    {
        return $this->belongsTo(NPC::class, 'related_npc_id');
    }
}
