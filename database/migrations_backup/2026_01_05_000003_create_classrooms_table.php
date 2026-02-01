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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key to teachers table (which references users.id)
            $table->unsignedInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            
            // Classroom attributes
            $table->string('class_name');
            $table->text('description')->nullable();
            $table->string('access_code')->unique()->comment('Unique code for student enrollment');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('teacher_id');
            $table->index('access_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
