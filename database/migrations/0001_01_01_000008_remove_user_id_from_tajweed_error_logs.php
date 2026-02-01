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
        Schema::table('tajweed_error_logs', function (Blueprint $table) {
            // Drop foreign key and index first
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            
            // Drop the user_id column
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tajweed_error_logs', function (Blueprint $table) {
            // Restore user_id column
            $table->unsignedInteger('user_id')->after('id');
            
            // Restore foreign key and index
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }
};
