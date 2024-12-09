<?php

declare(strict_types=1);

namespace App\Usecase\Job;

use App\Repositories\DlYoutube\DlYoutubeRepositoryInterface;
use App\Repositories\Youtube\YoutubeRepositoryInterface;
use Illuminate\Support\Facades\Log;

class RunYoutubeJobUsecase implements RunYoutubeJobUsecaseInterface
{
    private ?string $pageToken;

    public function __construct(
        private readonly YoutubeRepositoryInterface $youtubeRepository,
        private readonly DlYoutubeRepositoryInterface $dlYoutubeRepository,
    ) {
        $this->pageToken = null;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): void
    {
        $this->youtubeData();
    }

    /**
     * Youtubeから取得したデータを加工・保存
     *
     * @return void
     */
    private function youtubeData()
    {
        $categories = [1, 10, 17, 20, 26, 22, 25];
        foreach ($categories as $category) {
            $categoryVideos = [];
            do {
                $categoryVideos = $this->fetchYoutubeData($category);
                Log::info($this->pageToken);

                if (!$this->pageToken) {
                    $this->storeYoutubeData($categoryVideos);
                }
            } while ($this->pageToken);
        }
    }

    /**
     * Youtubeデータ取得・nextPageToken格納
     *
     * @param int $category
     * @param array $categoryVideos
     * @param mixed $pageToken
     * @return array
     */
    private function fetchYoutubeData(int $category): array
    {
        $response = $this->youtubeRepository->fetchYoutubeData($category, $this->pageToken);
        $this->pageToken = $response['nextPageToken'] ?? null;
        return $response['items'];
    }

    /**
     * 整形後、データを保存
     * @param mixed $categoryVideos
     * @return void
     */
    private function storeYoutubeData($categoryVideos)
    {
        $categoryVideosData = $this->formatYoutubeData($categoryVideos);
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
}
