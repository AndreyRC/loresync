<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['character_id', 'player_user_id', 'campaign_id'])]
class PlayerProfile extends Model
{
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
