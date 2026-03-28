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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('type'); // npc | player
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });

        Schema::create('character_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('key');
            $table->text('value');
            $table->timestamps();

            $table->index(['character_id', 'key']);
        });

        Schema::create('character_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('related_character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('type');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['character_id', 'type']);
            $table->index(['related_character_id', 'type']);
        });

        Schema::create('character_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['character_id', 'item_id']);
        });

        Schema::create('campaign_character', function (Blueprint $table) {
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();

            $table->unique(['campaign_id', 'character_id']);
        });

        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['character_id', 'campaign_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_profiles');
        Schema::dropIfExists('campaign_character');
        Schema::dropIfExists('character_item');
        Schema::dropIfExists('character_relationships');
        Schema::dropIfExists('character_attributes');
        Schema::dropIfExists('characters');
    }
};
