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
        // Assignments table
        Schema::create('assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->unsignedBigInteger('class_id');
            $table->dateTime('due_date');
            $table->text('instructions')->nullable();
            $table->integer('total_marks');
            $table->boolean('is_voice_submission')->default(true);
            
            // Quran-specific fields
            $table->string('surah')->nullable();
            $table->integer('start_verse')->nullable();
            $table->integer('end_verse')->nullable();
            
            // Tajweed rules fields
            $table->json('tajweed_rules')->nullable()->comment('JSON array of Tajweed rules to focus on');
            
            $table->timestamps();

            $table->foreign('material_id')->references('material_id')->on('materials')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classrooms')->onDelete('cascade');
        });

        // Assignment submissions table
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedInteger('student_id');
            $table->string('audio_file_path')->nullable();
            $table->text('text_submission')->nullable();
            $table->text('transcription')->nullable()->comment('Audio transcription for analysis');
            $table->dateTime('submitted_at');
            $table->enum('status', ['pending', 'submitted', 'graded', 'late'])->default('pending');
            
            // Tajweed analysis fields
            $table->json('tajweed_analysis')->nullable()->comment('Detailed Tajweed analysis results');
            
            $table->timestamps();

            $table->foreign('assignment_id')->references('assignment_id')->on('assignments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });

        // Scores table
        Schema::create('scores', function (Blueprint $table) {
            $table->id('score_id');
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedInteger('user_id');
            $table->integer('score');
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->foreign('assignment_id')->references('assignment_id')->on('assignments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['assignment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
