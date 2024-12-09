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
        Schema::create('dwh_youtube_videos', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('video_id')->unique()->comment('Youtube動画ID');
            $table->string('title')->comment('タイトル');
            $table->text('description')->nullable()->comment('概要欄');
            $table->string('channel_id')->comment('チャンネルID');
            $table->string('channel_name')->comment('チャンネル名');
            $table->unsignedBigInteger('view_count')->nullable()->comment('再生回数');
            $table->unsignedBigInteger('like_count')->nullable()->comment('いいね数');
            $table->unsignedBigInteger('comment_count')->nullable()->comment('コメント数');
            $table->string(column: 'url')->comment('動画URL');
            $table->timestamp('published_at')->nullable()->comment('公開日時');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dwh_youtube_videos');
    }
};
