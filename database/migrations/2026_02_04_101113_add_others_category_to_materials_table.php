<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Note: No schema changes needed. The 'category' column is already a string
     * that accepts any value. This migration exists for documentation purposes
     * to track when the 'Others' category was added to the application logic.
     * 
     * Categories supported: Madd Rules, Idgham Billa Ghunnah, Idgham Bi Ghunnah, Others
     */
    public function up(): void
    {
        // No schema changes needed - category column already supports any string value
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            //
        });
    }
};
