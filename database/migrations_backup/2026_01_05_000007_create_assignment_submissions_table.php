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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedInteger('student_id');
            $table->text('submission_text')->nullable();
            $table->string('file_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->enum('status', ['pending', 'submitted', 'graded'])->default('pending');
            $table->integer('marks_obtained')->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('assignment_id')->references('assignment_id')->on('assignments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
