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
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('telegram_id')->unique();
            $table->string('telegram_username')->nullable();
            $table->string('telegram_first_name')->nullable();
            $table->string('telegram_last_name')->nullable();
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamp('last_interaction')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('telegram_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
