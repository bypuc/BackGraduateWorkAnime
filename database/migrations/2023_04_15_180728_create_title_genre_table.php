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
        Schema::create('title_genre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('title_id')->onDelete('cascade')->cascadeOnUpdate();
            $table->foreignId('genre_id')->onDelete('cascade')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('title_genre');
    }
};
