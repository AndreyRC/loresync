<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['map_id', 'name', 'description'])]
class MapPin extends Model
{
    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }
}
