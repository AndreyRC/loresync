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
        Schema::create('npc_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('npc_id')->constrained('npcs')->cascadeOnDelete();
            $table->foreignId('related_npc_id')->constrained('npcs')->cascadeOnDelete();
            $table->string('type');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['npc_id', 'type']);
            $table->index(['related_npc_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('npc_relationships');
    }
};
