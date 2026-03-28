<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['character_id', 'key', 'value'])]
class CharacterAttribute extends Model
{
    protected $table = 'character_attributes';

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
