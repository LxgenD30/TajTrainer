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
        Schema::create('material_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('material_id');
            $table->enum('type', ['file', 'youtube', 'url']);
            $table->string('path')->nullable(); // File path or URL or YouTube link
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('material_id')->references('material_id')->on('materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_items', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
        });
        Schema::dropIfExists('material_items');
    }
};
