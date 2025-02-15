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
        Schema::table('dwh_youtube_videos', function (Blueprint $table) {
            $table->dropUnique(['ranking', 'search_category_id', 'created_at']);
            $table->dropColumn('id');

            $table->unique(['created_at', 'search_category_id', 'ranking'], 'unique_created_search_ranking');
            $table->index(['created_at', 'search_category_id', 'ranking'], 'index_created_search_ranking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dwh_youtube_videos', function (Blueprint $table) {
            $table->dropIndex('index_created_search_ranking');
            $table->dropUnique('unique_created_search_ranking');

            $table->unique(['ranking', 'search_category_id', 'created_at']);
        });
    }
};
