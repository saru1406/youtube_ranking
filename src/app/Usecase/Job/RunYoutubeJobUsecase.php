<?php

declare(strict_types=1);

namespace App\Usecase\Job;

use App\Repositories\DlYoutube\DlYoutubeRepositoryInterface;
use App\Repositories\DwhYoutube\DwhYoutubeRepositoryInterface;
use App\Repositories\Youtube\YoutubeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RunYoutubeJobUsecase implements RunYoutubeJobUsecaseInterface
{
    private ?string $pageToken;

    private array $allVideos;

    public function __construct(
        private readonly YoutubeRepositoryInterface $youtubeRepository,
        private readonly DlYoutubeRepositoryInterface $dlYoutubeRepository,
        private readonly DwhYoutubeRepositoryInterface $dwhYoutubeRepository,
    ) {
        $this->pageToken = null;
        $this->allVideos = [];
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): void
    {
        $this->storeDlYoutubeData();
        Log::info('DL保存完了');

        $this->storeDwhYoutubeData();
        Log::info('DWH保存完了');
    }

    /**
     * Youtubeから取得したデータを加工・保存
     *
     * @return void
     */
    private function storeDlYoutubeData(): void
    {
        $categories = [1, 10, 17, 20, 26, 22, 25];

        foreach ($categories as $category) {
            $categoryVideos = [];
            do {
                $categoryVideos = $this->fetchYoutubeData($category);

                if (! $this->pageToken) {
                    $this->storeYoutubeData($categoryVideos);
                    $this->allVideos = array_merge($this->allVideos, $categoryVideos);
                }
            } while ($this->pageToken);
        }
    }

    /**
     * Youtubeデータ取得・nextPageToken格納
     *
     * @param int $category
     * @param array $categoryVideos
     * @param ?string $pageToken
     * @return array
     */
    private function fetchYoutubeData(int $category): array
    {
        $response = $this->youtubeRepository->fetchYoutubeData($category, $this->pageToken);
        $this->pageToken = $response['nextPageToken'] ?? null;

        return $response['items'];
    }

    /**
     * 整形後、データをDLに保存
     *
     * @param array $categoryVideos
     * @return void
     */
    private function storeYoutubeData(array $categoryVideos)
    {
        $categoryVideosData = $this->formatYoutubeData(categoryVideos: $categoryVideos);
        $this->dlYoutubeRepository->bulkInsert($categoryVideosData);
    }

    /**
     * 取得したデータをJSONに整形し、配列に格納
     *
     * @param array $categoryVideos
     * @return array
     */
    private function formatYoutubeData(array $categoryVideos): array
    {
        return array_map(function ($categoryVideo) {
            return [
                'video_data' => json_encode($categoryVideo),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $categoryVideos);
    }

    /**
     * 整形後、データをDWHに保存
     *
     * @return void
     */
    private function storeDwhYoutubeData(): void
    {
        $formatDwhYoutubeData = $this->formatDwhYoutubeData();
        $this->dwhYoutubeRepository->bulkInsert($formatDwhYoutubeData);
    }

    /**
     * 取得したデータをDWH用に整形
     *
     * @return array
     */
    private function formatDwhYoutubeData(): array
    {
        return array_map(function ($allVideo) {
            return [
                'video_id' => $allVideo['id'],
                'title' => $allVideo['snippet']['title'],
                'description' => $allVideo['snippet']['description'] ?? null,
                'channel_id' => $allVideo['snippet']['channelId'],
                'channel_name' => $allVideo['snippet']['channelTitle'],
                'view_count' => $allVideo['statistics']['viewCount'] ?? 0,
                'like_count' => $allVideo['statistics']['likeCount'] ?? 0,
                'comment_count' => $allVideo['statistics']['commentCount'] ?? 0,
                'url' => "https://www.youtube.com/embed/{$allVideo['id']}",
                'published_at' => Carbon::parse($allVideo['snippet']['publishedAt'] ?? null)->format('Y-m-d H:i:s'),
            ];
        }, $this->allVideos);
    }
}
