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
        // Teachers table
        Schema::create('teachers', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('name');
            $table->text('biodata')->nullable();
            $table->string('title')->comment('e.g., Ustaz, Sheikh, Ustazah');
            
            $table->timestamps();
        });

        // Students table
        Schema::create('students', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('name');
            $table->text('biodata')->nullable();
            $table->string('current_level')->default('beginner');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('teachers');
    }
};
