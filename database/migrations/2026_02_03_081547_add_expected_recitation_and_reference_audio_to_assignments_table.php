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
            $table->text('expected_recitation')->nullable()->after('tajweed_rules')
                ->comment('Expected Arabic text from Quran for this assignment');
            $table->string('reference_audio_url')->nullable()->after('expected_recitation')
                ->comment('URL to reference audio (Alafasy recitation)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['expected_recitation', 'reference_audio_url']);
        });
    }
};
