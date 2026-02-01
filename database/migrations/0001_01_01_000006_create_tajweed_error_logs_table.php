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
        // Drop table if exists (in case of partial migration)
        Schema::dropIfExists('tajweed_error_logs');
        
        Schema::create('tajweed_error_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id'); // Match users table id type (increments)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('session_type'); // 'assignment', 'practice', 'self-practice'
            $table->unsignedBigInteger('session_id')->nullable(); // assignment_id or practice_session_id
            $table->string('error_type'); // 'madd', 'noon_sakin'
            $table->string('rule_name')->nullable(); // 'Idhar', 'Idgham', 'Iqlab', 'Ikhfa', 'Natural Madd', etc.
            $table->decimal('timestamp_in_audio', 8, 2)->nullable(); // Position in audio file (seconds)
            $table->enum('severity', ['minor', 'moderate', 'major'])->default('moderate');
            $table->boolean('was_correct')->default(false); // true = correct, false = error
            $table->text('issue_description')->nullable(); // What was wrong
            $table->text('recommendation')->nullable(); // How to fix
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('user_id');
            $table->index('session_type');
            $table->index('error_type');
            $table->index('was_correct');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tajweed_error_logs');
    }
};
