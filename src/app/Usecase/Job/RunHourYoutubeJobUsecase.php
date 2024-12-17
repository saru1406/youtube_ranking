<?php

declare(strict_types=1);

namespace App\Usecase\Job;

use App\Enums\Category\CategoryEnum;
use App\Repositories\DlYoutube\DlYoutubeRepositoryInterface;
use App\Repositories\DwhYoutube\DwhYoutubeRepositoryInterface;
use App\Repositories\Youtube\YoutubeRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DomainException;
use Illuminate\Support\Facades\Log;

class RunHourYoutubeJobUsecase implements RunHourYoutubeJobUsecaseInterface
{
    /**
     * ページトークン
     *
     * @var
     */
    private ?string $pageToken;

    /**
     * 全ビデオデータ
     *
     * @var array
     */
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
        $this->handle();

        $this->storeDlYoutubeData();
        Log::info('DL保存完了');

        $this->storeDwhYoutubeData();
        Log::info('DWH保存完了');
    }

    /**
     * ジョブが実行されていないことを確認
     *
     * @throws DomainException
     *
     * @return void
     */
    private function handle(): void
    {
        $exists = $this->dlYoutubeRepository->existsByDateHour(Carbon::now()->format('Y-m-d H'));
        if ($exists) {
            throw new DomainException('現時刻のジョブは既に実行されています。');
        }

    }

    /**
     * Youtubeから取得したデータを加工・保存
     *
     * @return void
     */
    private function storeDlYoutubeData(): void
    {
        $categories = CategoryEnum::toArray();

        foreach ($categories as $category) {
            $categoryVideos = [];
            do {
                $categoryVideos = array_merge($categoryVideos, $this->fetchYoutubeData($category));

                if (! $this->pageToken) {
                    $this->storeYoutubeData($categoryVideos, $category);
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

        return array_map(function ($data) use ($category) {
            return [
                ...$data,
                'search_category_id' => $category,
            ];
        }, $response['items']);
    }

    /**
     * 整形後、データをDLに保存
     *
     * @param array $categoryVideos
     * @return void
     */
    private function storeYoutubeData(array $categoryVideos, int $category)
    {
        $categoryVideosData = $this->formatYoutubeData($categoryVideos, $category);
        $this->dlYoutubeRepository->bulkInsert($categoryVideosData);
    }

    /**
     * 取得したデータをJSONに整形し、配列に格納
     *
     * @param array $categoryVideos
     * @return array
     */
    private function formatYoutubeData(array $categoryVideos, int $category): array
    {
        return array_map(function ($categoryVideo) use ($category) {
            return [
                'search_category_id' => $category,
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
                'search_category_id' => $allVideo['search_category_id'],
                'video_id' => $allVideo['id'],
                'title' => $allVideo['snippet']['title'],
                'description' => $allVideo['snippet']['description'] ?? null,
                'channel_id' => $allVideo['snippet']['channelId'],
                'channel_name' => $allVideo['snippet']['channelTitle'],
                'view_count' => $allVideo['statistics']['viewCount'] ?? 0,
                'like_count' => $allVideo['statistics']['likeCount'] ?? 0,
                'comment_count' => $allVideo['statistics']['commentCount'] ?? 0,
                'category_id' => $allVideo['snippet']['categoryId'] ?? null,
                'url' => "https://www.youtube.com/watch?v={$allVideo['id']}",
                'published_at' => Carbon::parse($allVideo['snippet']['publishedAt'] ?? null)->format('Y-m-d H:i:s'),
                'duration' => $this->isoFormat($allVideo['contentDetails']['duration']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $this->allVideos);
    }

    /**
     * isoを時間に変換
     *
     * @param mixed $isoDuration
     * @return string
     */
    private function isoFormat($isoDuration)
    {
        $interval = CarbonInterval::makeFromString($isoDuration);

        $formatted = '';

        if ($interval->h > 0) {
            $formatted .= "{$interval->h}時間";
        }
        if ($interval->i > 0) {
            $formatted .= "{$interval->i}分";
        }
        if ($interval->s > 0) {
            $formatted .= "{$interval->s}秒";
        }

        return $formatted;
    }
}
