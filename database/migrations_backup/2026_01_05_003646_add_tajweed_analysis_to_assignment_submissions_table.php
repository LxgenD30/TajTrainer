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
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->json('tajweed_analysis')->nullable()->after('transcription');
            $table->decimal('tajweed_score', 5, 2)->nullable()->after('tajweed_analysis');
            $table->string('tajweed_grade', 50)->nullable()->after('tajweed_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropColumn(['tajweed_analysis', 'tajweed_score', 'tajweed_grade']);
        });
    }
};
