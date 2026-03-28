<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['npc_id', 'key', 'value'])]
class NPCAttribute extends Model
{
    protected $table = 'npc_attributes';

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NPC::class);
    }
}
