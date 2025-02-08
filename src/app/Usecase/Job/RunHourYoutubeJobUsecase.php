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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Str;

class RunHourYoutubeJobUsecase implements RunHourYoutubeJobUsecaseInterface
{
    /**
     * ULID
     *
     * @var string
     */
    private string $ulid;

    /**
     * ページトークン
     *
     * @var
     */
    private ?string $pageToken;

    /**
     * 現在時刻
     *
     * @var Carbon
     */
    private Carbon $now;

    public function __construct(
        private readonly YoutubeRepositoryInterface $youtubeRepository,
        private readonly DlYoutubeRepositoryInterface $dlYoutubeRepository,
        private readonly DwhYoutubeRepositoryInterface $dwhYoutubeRepository,
    ) {
        $this->ulid = (string) Str::ulid();
        $this->pageToken = null;
        $this->now = Carbon::now()->addHour()->startOfHour();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): void
    {
        // $this->handle();

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
    private function storeYoutubeData(array $categoryVideos, int $category): void
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
        return array_map(function ($categoryVideo, $key) use ($category) {
            return [
                'ulid' => $this->ulid,
                'ranking' => $key + 1,
                'search_category_id' => $category,
                'video_data' => json_encode($categoryVideo),
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }, $categoryVideos, array_keys($categoryVideos));
    }

    /**
     * 整形後、データをDWHに保存
     *
     * @return void
     */
    private function storeDwhYoutubeData(): void
    {
        $dlYoutubeData = $this->dlYoutubeRepository->fetchDlYoutubeByUlid($this->ulid);
        $formatDwhYoutubeData = $this->formatDwhYoutubeData($dlYoutubeData);
        $this->dwhYoutubeRepository->bulkInsert($formatDwhYoutubeData);
    }

    /**
     * 取得したデータをDWH用に整形
     *
     * @return array
     */
    private function formatDwhYoutubeData(Collection $dlYoutubeData): array
    {
        return $dlYoutubeData->map(function ($dlYoutube) {
            return [
                'ranking' => $dlYoutube['ranking'],
                'search_category_id' => $dlYoutube['search_category_id'],
                'video_id' => $dlYoutube['video_data']['id'],
                'title' => $dlYoutube['video_data']['snippet']['title'],
                'description' => $dlYoutube['video_data']['snippet']['description'] ?? null,
                'channel_id' => $dlYoutube['video_data']['snippet']['channelId'],
                'channel_name' => $dlYoutube['video_data']['snippet']['channelTitle'],
                'view_count' => $dlYoutube['video_data']['statistics']['viewCount'] ?? 0,
                'like_count' => $dlYoutube['video_data']['statistics']['likeCount'] ?? 0,
                'comment_count' => $dlYoutube['video_data']['statistics']['commentCount'] ?? 0,
                'category_id' => $dlYoutube['video_data']['snippet']['categoryId'] ?? null,
                'url' => "https://www.youtube.com/watch?v={$dlYoutube['video_data']['id']}",
                'published_at' => Carbon::parse($dlYoutube['video_data']['snippet']['publishedAt'] ?? null)->format('Y-m-d H:i:s'),
                'duration' => $this->isoFormat($dlYoutube['video_data']['contentDetails']['duration']),
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        })->toArray();
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
