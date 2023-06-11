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
        Schema::table('title_genre', function (Blueprint $table) {
            $table->foreign('title_id')->references('id')->on('titles');
            $table->foreign('genre_id')->references('id')->on('genres');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('title_genre', function (Blueprint $table) {
            $table->dropForeign('title_genre_title_id_foreign');
            $table->dropForeign('title_genre_genre_id_foreign');
        });
    }
};
