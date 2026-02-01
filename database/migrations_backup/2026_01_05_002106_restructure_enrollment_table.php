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
        // Drop and recreate the table with proper structure
        Schema::dropIfExists('classroom_student');
        
        Schema::create('enrollment', function (Blueprint $table) {
            $table->id('enrollment_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('date_joined')->useCurrent();
            $table->timestamps();

            // Foreign keys
            $table->foreign('class_id')
                  ->references('id')
                  ->on('classrooms')
                  ->onDelete('cascade');
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment');
        
        // Recreate old table structure
        Schema::create('classroom_student', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            
            $table->primary(['class_id', 'user_id']);
            
            $table->foreign('class_id')
                  ->references('id')
                  ->on('classrooms')
                  ->onDelete('cascade');
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
