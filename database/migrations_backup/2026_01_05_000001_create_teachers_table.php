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
        Schema::create('teachers', function (Blueprint $table) {
            // Primary Key that is also a Foreign Key to users.id
            $table->unsignedInteger('id')->primary();
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
            
            // Teacher-specific attributes
            $table->string('name');
            $table->text('biodata')->nullable();
            $table->string('title')->comment('e.g., Ustaz, Sheikh, Ustazah');
            $table->string('specialization');
            $table->string('ijazah_path')->nullable()->comment('Path to Ijazah certificate');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
