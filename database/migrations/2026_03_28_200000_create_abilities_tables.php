<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });

        Schema::create('ability_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ability_id')->constrained('abilities')->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->index(['ability_id', 'key']);
        });

        Schema::create('ability_character', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('ability_id')->constrained('abilities')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['character_id', 'ability_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ability_character');
        Schema::dropIfExists('ability_attributes');
        Schema::dropIfExists('abilities');
    }
};
