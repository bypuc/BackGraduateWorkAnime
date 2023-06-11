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
        Schema::create('user_titles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('title_id')->references('id')->on('titles')->onDelete('cascade');
            $table->enum('type', ['rate', 'enter', 'watch', 'like']);
            $table->integer('rate')->nullable();
            $table->integer('season')->nullable();
            $table->integer('episode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_titles');
    }
};
