<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ability_id', 'key', 'value'])]
class AbilityAttribute extends Model
{
    protected $table = 'ability_attributes';

    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }
}
