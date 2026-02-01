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
        Schema::table('assignments', function (Blueprint $table) {
            // Make material_id nullable (optional)
            $table->unsignedBigInteger('material_id')->nullable()->change();
            
            // Add Quran verse fields
            $table->string('surah')->nullable()->after('material_id');
            $table->integer('start_verse')->nullable()->after('surah');
            $table->integer('end_verse')->nullable()->after('start_verse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('material_id')->nullable(false)->change();
            $table->dropColumn(['surah', 'start_verse', 'end_verse']);
        });
    }
};
