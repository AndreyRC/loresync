<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaign_npc', function (Blueprint $table) {
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained('npcs')->cascadeOnDelete();
            $table->unique(['campaign_id', 'entity_id']);
        });

        Schema::create('campaign_location', function (Blueprint $table) {
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained('locations')->cascadeOnDelete();
            $table->unique(['campaign_id', 'entity_id']);
        });

        Schema::create('campaign_item', function (Blueprint $table) {
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained('items')->cascadeOnDelete();
            $table->unique(['campaign_id', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_item');
        Schema::dropIfExists('campaign_location');
        Schema::dropIfExists('campaign_npc');
    }
};
