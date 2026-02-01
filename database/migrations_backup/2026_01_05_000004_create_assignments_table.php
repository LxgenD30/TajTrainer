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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('class_id');
            $table->dateTime('due_date');
            $table->text('instructions');
            $table->integer('total_marks');
            $table->boolean('is_voice_submission')->default(true);
            $table->timestamps();

            $table->foreign('material_id')->references('material_id')->on('materials')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classrooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
