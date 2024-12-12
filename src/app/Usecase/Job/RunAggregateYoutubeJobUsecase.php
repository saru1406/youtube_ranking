<?php

declare(strict_types=1);

namespace App\Usecase\Job;

use App\Repositories\DailyYoutube\DailyYoutubeRepositoryInterface;
use App\Repositories\DwhYoutube\DwhYoutubeRepositoryInterface;
use Illuminate\Support\Facades\Log;

class RunAggregateYoutubeJobUsecase implements RunAggregateYoutubeJobUsecaseInterface
{
    /**
     * チャンクサイズ
     *
     * @var int
     */
    private int $chunkSize;

    public function __construct(
        private readonly DwhYoutubeRepositoryInterface $dwhYoutubeRepository,
        private readonly DailyYoutubeRepositoryInterface $dailyYoutubeRepository,
    ) {
        $this->chunkSize = 1500;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): void
    {
        $this->dailyAggregateYoutubeData();
        Log::info('日別集計保存完了');

        // $this->storeDwhYoutubeData();
        // Log::info('DWH保存完了');
    }

    /**
     * DWHYoutubeから取得したデータを日別に集計
     *
     * @return array
     */
    private function dailyAggregateYoutubeData(): void
    {
        $dailyAggregateYoutubeData = $this->dwhYoutubeRepository->fetchDwhYoutubeByDailyAggregate();
        $dailyAggregateYoutubeToArray = $this->dailyAggregateYoutubeDataFormat($dailyAggregateYoutubeData);
        $this->storeDailyAggregateYoutubeData($dailyAggregateYoutubeToArray);
    }

    /**
     * 日別Youtube集計データを整形
     *
     * @param array $dailyAggregateYoutubeData
     * @return array
     */
    private function dailyAggregateYoutubeDataFormat(array $dailyAggregateYoutubeData): array
    {
        return array_map(function ($dailyAggregateYoutube) {
            return [
                'search_category_id' => $dailyAggregateYoutube->search_category_id,
                'target_date' => $dailyAggregateYoutube->target_date,
                'daily_view_count' => $dailyAggregateYoutube->daily_view_count,
                'video_id' => $dailyAggregateYoutube->video_id,
                'title' => $dailyAggregateYoutube->title,
                'description' => $dailyAggregateYoutube->description,
                'channel_id' => $dailyAggregateYoutube->channel_id,
                'channel_name' => $dailyAggregateYoutube->channel_name,
                'view_count' => $dailyAggregateYoutube->view_count,
                'like_count' => $dailyAggregateYoutube->like_count,
                'comment_count' => $dailyAggregateYoutube->comment_count,
                'category_id' => $dailyAggregateYoutube->category_id,
                'url' => $dailyAggregateYoutube->url,
                'duration' => $dailyAggregateYoutube->duration,
                'published_at' => $dailyAggregateYoutube->published_at,
            ];
        }, $dailyAggregateYoutubeData);
    }

    /**
     * 日別Youtube集計データを保存
     *
     * @param array $dailyAggregateYoutubeToArray
     * @return void
     */
    private function storeDailyAggregateYoutubeData(array $dailyAggregateYoutubeToArray): void
    {
        $chunksData = array_chunk($dailyAggregateYoutubeToArray, $this->chunkSize);

        foreach ($chunksData as $chunkData) {
            $this->dailyYoutubeRepository->bulkInsert($chunkData);
        }
    }
}
