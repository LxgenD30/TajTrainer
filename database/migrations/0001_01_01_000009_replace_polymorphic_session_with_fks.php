<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Replace polymorphic session_type/session_id with proper foreign keys
     */
    public function up(): void
    {
        Schema::table('tajweed_error_logs', function (Blueprint $table) {
            // Drop old polymorphic columns and their indexes
            $table->dropIndex(['session_type']);
            $table->dropColumn(['session_type', 'session_id']);
            
            // Add proper foreign key columns
            $table->unsignedBigInteger('practice_session_id')->nullable()->after('id');
            $table->unsignedBigInteger('assignment_submission_id')->nullable()->after('practice_session_id');
            
            // Add foreign key constraints
            $table->foreign('practice_session_id')
                ->references('id')
                ->on('practice_sessions')
                ->onDelete('cascade');
                
            $table->foreign('assignment_submission_id')
                ->references('id')
                ->on('assignment_submissions')
                ->onDelete('cascade');
            
            // Add indexes for performance
            $table->index('practice_session_id');
            $table->index('assignment_submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tajweed_error_logs', function (Blueprint $table) {
            // Drop foreign keys and indexes
            $table->dropForeign(['practice_session_id']);
            $table->dropForeign(['assignment_submission_id']);
            $table->dropIndex(['practice_session_id']);
            $table->dropIndex(['assignment_submission_id']);
            
            // Drop the columns
            $table->dropColumn(['practice_session_id', 'assignment_submission_id']);
            
            // Restore old polymorphic structure
            $table->string('session_type')->after('id');
            $table->unsignedBigInteger('session_id')->nullable()->after('session_type');
            $table->index('session_type');
        });
    }
};
