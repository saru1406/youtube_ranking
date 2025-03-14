<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dl_youtube_videos', function (Blueprint $table) {
            $table->ulid()->comment('ulid');
            $table->unsignedTinyInteger('ranking')->comment('ランキング');
            $table->unsignedTinyInteger('search_category_id')->comment('検索カテゴリID');
            $table->foreign('search_category_id')->references('category_number')->on('categories')->onUpdate('cascade');
            $table->json('video_data')->comment('動画情報');
            $table->timestamps();

            $table->unique(['ulid', 'ranking', 'search_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dl_youtube_videos');
    }
};
