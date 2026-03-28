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
        Schema::table('npcs', function (Blueprint $table) {
            if (! Schema::hasColumn('npcs', 'title')) {
                $table->string('title')->nullable()->after('name');
            }

            if (! Schema::hasColumn('npcs', 'status')) {
                $table->string('status')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            if (Schema::hasColumn('npcs', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('npcs', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
