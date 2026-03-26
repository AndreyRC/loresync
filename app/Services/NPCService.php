<?php

namespace App\Services;

use App\Models\NPC;
use App\Models\User;

class NPCService
{
    /**
     * @param array{location_id?:int|null,name:string,description?:string|null,image_path?:string|null} $data
     */
    public function create(User $owner, array $data): NPC
    {
        return NPC::create([
            'user_id' => $owner->id,
            'location_id' => $data['location_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image_path' => $data['image_path'] ?? null,
        ]);
    }
}
