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
        Schema::create('dwh_daily_youtube_videos', function (Blueprint $table) {
            $table->ulid()->comment('ulid');
            $table->year('post_year')->comment('投稿年');
            $table->unsignedTinyInteger('post_month')->comment('投稿月');
            $table->unsignedTinyInteger('post_day')->comment('投稿日');
            $table->unsignedTinyInteger('post_week')->comment('投稿週');
            $table->unsignedTinyInteger('post_week_day')->comment('投稿曜日');
            $table->year('target_year')->comment('対象年');
            $table->unsignedTinyInteger('target_month')->comment('投稿月');
            $table->unsignedTinyInteger('target_week')->comment('対象週');
            $table->unsignedTinyInteger('target_week_day')->comment('対象曜日');
            $table->unsignedTinyInteger('search_category_id')->comment('検索カテゴリID');
            $table->foreign('search_category_id')->references('category_number')->on('categories')->onUpdate('cascade');
            $table->string('video_id')->comment('Youtube動画ID');
            $table->string('title')->comment('タイトル');
            $table->text('description')->nullable()->comment('概要欄');
            $table->string('channel_id')->comment('チャンネルID');
            $table->string('channel_name')->comment('チャンネル名');
            $table->unsignedBigInteger('view_count')->nullable()->comment('再生回数');
            $table->unsignedBigInteger('like_count')->nullable()->comment('いいね数');
            $table->unsignedBigInteger('comment_count')->nullable()->comment('コメント数');
            $table->unsignedTinyInteger('category_id')->nullable()->comment('カテゴリID');
            $table->string(column: 'url')->comment('動画URL');
            $table->string('duration')->comment('再生時間');
            $table->timestamp('published_at')->nullable()->comment('公開日時');
            $table->timestamps();

            $table->unique(['created_at', 'search_category_id', 'video_id'], 'unique_dwh_daily_youtube_video');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dwh_daily_youtube_videos');
    }
};
