<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['character_id', 'related_character_id', 'type', 'description'])]
class CharacterRelationship extends Model
{
    protected $table = 'character_relationships';

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function relatedCharacter(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'related_character_id');
    }
}
