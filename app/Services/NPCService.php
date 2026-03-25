<?php

namespace App\Services;

use App\Models\NPC;

class NPCService
{
    /**
     * @param array{campaign_id:int,location_id?:int|null,name:string,description?:string|null} $data
     */
    public function create(array $data): NPC
    {
        return NPC::create([
            'campaign_id' => $data['campaign_id'],
            'location_id' => $data['location_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }
}
