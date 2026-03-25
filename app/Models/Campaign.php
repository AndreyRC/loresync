<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'description'])]
class Campaign extends Model
{
    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function npcs(): HasMany
    {
        return $this->hasMany(NPC::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
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
