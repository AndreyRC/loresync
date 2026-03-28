<?php

use App\Models\Character;
use App\Models\NPC;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('npcs') || ! Schema::hasTable('characters')) {
            return;
        }

        // 1) Copy NPC rows into characters (preserve IDs to avoid breaking taggable IDs, relationships, etc.).
        DB::table('npcs')
            ->orderBy('id')
            ->select([
                'id',
                'user_id',
                'location_id',
                'name',
                'title',
                'description',
                'status',
                'image_path',
                'created_at',
                'updated_at',
            ])
            ->chunkById(500, function ($npcs) {
                $rows = [];

                foreach ($npcs as $npc) {
                    $rows[] = [
                        'id' => $npc->id,
                        'user_id' => $npc->user_id,
                        'location_id' => $npc->location_id,
                        'name' => $npc->name,
                        'title' => $npc->title,
                        'description' => $npc->description,
                        'status' => $npc->status,
                        'type' => Character::TYPE_NPC,
                        'image_path' => $npc->image_path,
                        'created_at' => $npc->created_at,
                        'updated_at' => $npc->updated_at,
                    ];
                }

                if ($rows !== []) {
                    DB::table('characters')->insertOrIgnore($rows);
                }
            });

        // 2) Copy attributes.
        if (Schema::hasTable('npc_attributes') && Schema::hasTable('character_attributes')) {
            DB::table('npc_attributes')
                ->select(['npc_id', 'key', 'value', 'created_at', 'updated_at'])
                ->orderBy('id')
                ->chunkById(1000, function ($attributes) {
                    $rows = [];

                    foreach ($attributes as $attribute) {
                        $rows[] = [
                            'character_id' => $attribute->npc_id,
                            'key' => $attribute->key,
                            'value' => $attribute->value,
                            'created_at' => $attribute->created_at,
                            'updated_at' => $attribute->updated_at,
                        ];
                    }

                    if ($rows !== []) {
                        DB::table('character_attributes')->insert($rows);
                    }
                });
        }

        // 3) Copy relationships.
        if (Schema::hasTable('npc_relationships') && Schema::hasTable('character_relationships')) {
            DB::table('npc_relationships')
                ->select(['npc_id', 'related_npc_id', 'type', 'description', 'created_at', 'updated_at'])
                ->orderBy('id')
                ->chunkById(1000, function ($relationships) {
                    $rows = [];

                    foreach ($relationships as $relationship) {
                        $rows[] = [
                            'character_id' => $relationship->npc_id,
                            'related_character_id' => $relationship->related_npc_id,
                            'type' => $relationship->type,
                            'description' => $relationship->description,
                            'created_at' => $relationship->created_at,
                            'updated_at' => $relationship->updated_at,
                        ];
                    }

                    if ($rows !== []) {
                        DB::table('character_relationships')->insert($rows);
                    }
                });
        }

        // 4) Copy campaign associations.
        if (Schema::hasTable('campaign_npc') && Schema::hasTable('campaign_character')) {
            $rows = DB::table('campaign_npc')
                ->select(['campaign_id', 'entity_id'])
                ->get()
                ->map(fn ($r) => ['campaign_id' => $r->campaign_id, 'character_id' => $r->entity_id])
                ->all();

            if ($rows !== []) {
                DB::table('campaign_character')->insertOrIgnore($rows);
            }
        }

        // 5) Retarget taggables from NPC to Character (IDs preserved, so taggable_id stays valid).
        if (Schema::hasTable('taggables')) {
            DB::table('taggables')
                ->where('taggable_type', NPC::class)
                ->update(['taggable_type' => Character::class]);
        }

        // 6) PostgreSQL: ensure the sequence is at least MAX(id) after explicit inserts.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('characters', 'id'), (SELECT COALESCE(MAX(id), 1) FROM characters))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank: this migration is a one-way data move.
    }
};
