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
        Schema::table('titles', function (Blueprint $table) {
            $table->string('big_image');
            $table->string('author');
            $table->string('studio');
            $table->string('release_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->dropColumn('big_image');
            $table->dropColumn('author');
            $table->dropColumn('studio');
            $table->dropColumn('release_date');
        });
    }
};
