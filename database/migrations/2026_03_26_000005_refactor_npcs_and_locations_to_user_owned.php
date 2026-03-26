<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->string('image_path')->nullable()->after('description');
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->string('image_path')->nullable()->after('description');
        });

        // Backfill user ownership and campaign associations based on the prior campaign_id.
        if (Schema::hasColumn('locations', 'campaign_id')) {
            // SQLite doesn't support UPDATE ... JOIN with assigning joined columns.
            // Use a correlated subquery for broad DB compatibility.
            DB::table('locations')->update([
                'user_id' => DB::raw('(select user_id from campaigns where campaigns.id = locations.campaign_id)'),
            ]);

            DB::table('campaign_location')->insertUsing(
                ['campaign_id', 'entity_id'],
                DB::table('locations')
                    ->select(['campaign_id', 'id'])
            );

            Schema::table('locations', function (Blueprint $table) {
                $table->dropForeign(['campaign_id']);
                $table->dropColumn('campaign_id');
            });
        }

        if (Schema::hasColumn('npcs', 'campaign_id')) {
            DB::table('npcs')->update([
                'user_id' => DB::raw('(select user_id from campaigns where campaigns.id = npcs.campaign_id)'),
            ]);

            DB::table('campaign_npc')->insertUsing(
                ['campaign_id', 'entity_id'],
                DB::table('npcs')
                    ->select(['campaign_id', 'id'])
            );

            Schema::table('npcs', function (Blueprint $table) {
                $table->dropForeign(['campaign_id']);
                $table->dropColumn('campaign_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            if (! Schema::hasColumn('npcs', 'campaign_id')) {
                $table->foreignId('campaign_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }

            if (Schema::hasColumn('npcs', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('npcs', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });

        Schema::table('locations', function (Blueprint $table) {
            if (! Schema::hasColumn('locations', 'campaign_id')) {
                $table->foreignId('campaign_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }

            if (Schema::hasColumn('locations', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('locations', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
