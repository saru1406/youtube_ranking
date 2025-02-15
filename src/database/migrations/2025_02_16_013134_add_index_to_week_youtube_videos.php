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
        Schema::table('week_youtube_videos', function (Blueprint $table) {
            $table->index(['target_year', 'target_week', 'search_category_id', 'ranking'], 'index_year_week_search_ranking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('week_youtube_videos', function (Blueprint $table) {
            $table->dropIndex('index_year_week_search_ranking');
        });
    }
};
