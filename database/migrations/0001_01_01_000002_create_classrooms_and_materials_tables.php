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
        // Materials table
        Schema::create('materials', function (Blueprint $table) {
            $table->id('material_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('video_link')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('type', ['pdf', 'video', 'audio', 'document'])->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        // Classrooms table
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            
            $table->string('class_name');
            $table->text('description')->nullable();
            $table->string('access_code')->unique()->comment('Unique code for student enrollment');
            
            $table->timestamps();
            
            $table->index('teacher_id');
            $table->index('access_code');
        });

        // Enrollment table (pivot table for classrooms and students)
        Schema::create('enrollment', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('class_id');
            $table->date('date_joined');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classrooms')->onDelete('cascade');
            
            $table->unique(['user_id', 'class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('materials');
    }
};
