<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\User;

class CampaignService
{
    /**
     * @param array{name:string,description?:string|null} $data
     */
    public function create(User $master, array $data): Campaign
    {
        return Campaign::create([
            'user_id' => $master->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }
}
