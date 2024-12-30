<?php

declare(strict_types=1);

namespace App\Usecase\Job;

use App\Repositories\DailyYoutube\DailyYoutubeRepositoryInterface;
use App\Repositories\DlDailyYoutube\DlDailyYoutubeRepositoryInterface;
use App\Repositories\DwhDailyYoutube\DwhDailyYoutubeRepositoryInterface;
use App\Repositories\DwhYoutube\DwhYoutubeRepositoryInterface;
use App\Repositories\MonthYoutube\MonthYoutubeRepositoryInterface;
use App\Repositories\WeekYoutube\WeekYoutubeRepositoryInterface;
use App\Repositories\Youtube\YoutubeRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Str;

class RunAggregateYoutubeJobUsecase implements RunAggregateYoutubeJobUsecaseInterface
{
    private string $ulid;

    /**
     * チャンクサイズ
     *
     * @var int
     */
    private int $chunkSize;

    private Carbon $now;

    /**
     * 年
     *
     * @var int
     */
    private int $year;

    /**
     * 月
     *
     * @var int
     */
    private int $month;

    /**
     * 週
     *
     * @var int
     */
    private int $week;

    /**
     * 曜日
     *
     * @var int
     */
    private int $weekDay;

    public function __construct(
        private readonly YoutubeRepositoryInterface $youtubeRepository,
        private readonly DwhYoutubeRepositoryInterface $dwhYoutubeRepository,
        private readonly DailyYoutubeRepositoryInterface $dailyYoutubeRepository,
        private readonly DlDailyYoutubeRepositoryInterface $dlDailyYoutubeRepository,
        private readonly DwhDailyYoutubeRepositoryInterface $dwhDailyYoutubeRepository,
        private readonly WeekYoutubeRepositoryInterface $weekYoutubeRepository,
        private readonly MonthYoutubeRepositoryInterface $monthYoutubeRepository,
    ) {
        $this->ulid = (string) Str::ulid();
        $this->chunkSize = 1500;
        $this->now = now();
        $this->year = $this->now->year;
        $this->month = $this->now->month;
        $this->week = $this->now->weekOfYear;
        $this->weekDay = $this->now->dayOfWeekIso;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): void
    {
        // $this->handle();

        $this->dlDailyYoutubeData();
        Log::info('DL日別保存完了');

        $this->dwhDailyYoutubeData();
        Log::info('DWH日別保存完了');

        // $this->dailyAggregateYoutubeData();
        // Log::info('DM日別集計保存完了');

        $this->weekAggregateYoutubeData();
        Log::info('DM週別集計保存完了');

        $this->monthAggregateYoutubeData();
        Log::info('DM月別集計保存完了');
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
        $exists = $this->dlDailyYoutubeRepository->existsByDate($this->now->format('Y-m-d'));
        if ($exists) {
            throw new DomainException('本日のジョブは既に実行されています。');
        }
    }

    /**
     * Youtubeから取得したデータを整形・保存
     *
     * @return void
     */
    private function dlDailyYoutubeData(): void
    {
        $videoIdsByCategory = $this->dwhYoutubeRepository->fetchDwhYoutubeVideoIds()->groupBy('search_category_id');

        $categoryVideos = $this->fetchDlDailyYoutubeData($videoIdsByCategory);
        $allVideos = $this->formatDlDailyYoutubeData($categoryVideos);
        $this->storeDlDailyYoutubeData($allVideos);
    }

    /**
     * DlDailyYoutubeDataを取得
     *
     * @param Collection $videoIdsByCategory
     * @return Collection
     */
    private function fetchDlDailyYoutubeData(Collection $videoIdsByCategory): Collection
    {
        return $videoIdsByCategory->map(function ($categoryVideoIds, $key) {
            return $categoryVideoIds->chunk(50)->map(function ($videoIds) {
                $videoIdString = $videoIds->pluck('video_id')->implode(',');

                return $this->youtubeRepository->fetchYoutubeDataByVideoId($videoIdString)['items'];
            });
        });
    }

    /**
     * DLDailyYoutubeデータを整形
     *
     * @param Collection $categoryVideos
     * @return Collection
     */
    private function formatDlDailyYoutubeData(Collection $categoryVideos): Collection
    {
        return $categoryVideos->map(function ($videos, $key) {
            return $videos->flatten(1)->map(function ($video) use ($key) {
                return [
                    'ulid' => $this->ulid,
                    'search_category_id' => $key,
                    'video_data' => json_encode($video),
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];
            });
        });
    }

    /**
     * DLDailyYoutubeデータ保存
     *
     * @param Collection $allVideos
     * @return void
     */
    private function storeDlDailyYoutubeData(Collection $allVideos): void
    {
        $allVideos->each(function ($videos) {
            $this->dlDailyYoutubeRepository->bulkInsert($videos->all());
        });
    }

    /**
     * DWHYoutubeから取得したデータをDWHYoutubeへ整形・保存
     *
     * @return void
     */
    private function dwhDailyYoutubeData()
    {
        $dlDailyYoutubeData = $this->dlDailyYoutubeRepository->fetchDlDailyYoutubeByUlid($this->ulid);
        $formatDlDailyYoutubeData = $this->formatDwhDailyYoutubeData($dlDailyYoutubeData)->all();
        $this->storeYoutubeData('dwh', $formatDlDailyYoutubeData);
    }

    /**
     * DWHDailyYoutubeデータを整形
     *
     * @param Collection $dlDailyYoutubeData
     * @return Collection
     */
    private function formatDwhDailyYoutubeData(Collection $dlDailyYoutubeData): Collection
    {
        return $dlDailyYoutubeData->map(function ($dlDailyYoutube) {
            $publishedAt = Carbon::parse($dlDailyYoutube->video_data['snippet']['publishedAt']);

            return [
                'ulid' => $this->ulid,
                'post_year' => $publishedAt->year,
                'post_month' => $publishedAt->month,
                'post_day' => $publishedAt->day,
                'post_week' => $publishedAt->weekOfYear,
                'post_week_day' => $publishedAt->dayOfWeekIso,
                'target_year' => $this->year,
                'target_month' => $this->month,
                'target_week' => $this->week,
                'target_week_day' => $this->weekDay,
                'search_category_id' => $dlDailyYoutube['search_category_id'],
                'video_id' => $dlDailyYoutube->video_data['id'],
                'title' => $dlDailyYoutube->video_data['snippet']['title'],
                'description' => $dlDailyYoutube->video_data['snippet']['description'] ?? null,
                'channel_id' => $dlDailyYoutube->video_data['snippet']['channelId'],
                'channel_name' => $dlDailyYoutube->video_data['snippet']['channelTitle'],
                'view_count' => $dlDailyYoutube->video_data['statistics']['viewCount'] ?? 0,
                'like_count' => $dlDailyYoutube->video_data['statistics']['likeCount'] ?? 0,
                'comment_count' => $dlDailyYoutube->video_data['statistics']['commentCount'] ?? 0,
                'category_id' => $dlDailyYoutube->video_data['snippet']['categoryId'] ?? null,
                'url' => "https://www.youtube.com/watch?v={$dlDailyYoutube->video_data['id']}",
                'published_at' => $publishedAt->format('Y-m-d H:i:s'),
                'duration' => $this->isoFormat($dlDailyYoutube->video_data['contentDetails']['duration']),
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        });
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

    /**
     * DWHYoutubeから取得したデータを日別に集計
     *
     * @return array
     */
    private function dailyAggregateYoutubeData(): void
    {
        $this->dailyYoutubeRepository->delete();
        $dailyAggregateYoutubeData = $this->dwhDailyYoutubeRepository->fetchDwhYoutubeByDailyAggregate();
        $dailyAggregateYoutubeToArray = $this->dailyAggregateYoutubeDataFormat($dailyAggregateYoutubeData);
        $this->storeYoutubeData('daily', $dailyAggregateYoutubeToArray);
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
     * DWHYoutubeから取得したデータを週別に集計
     *
     * @return array
     */
    private function weekAggregateYoutubeData(): void
    {
        $this->weekYoutubeRepository->delete();
        $weekAggregateYoutubeData = $this->dwhDailyYoutubeRepository->fetchDwhYoutubeByWeekAggregate();
        $weekAggregateYoutubeToArray = $this->weekAggregateYoutubeDataFormat($weekAggregateYoutubeData);
        $this->storeYoutubeData('week', $weekAggregateYoutubeToArray);
    }

    /**
     * DWHYoutubeから取得したデータを週別に集計
     *
     * @return array
     */
    private function monthAggregateYoutubeData(): void
    {
        $this->monthYoutubeRepository->delete();
        $monthAggregateYoutubeData = $this->dwhDailyYoutubeRepository->fetchDwhYoutubeByMonthAggregate();
        $monthAggregateYoutubeToArray = $this->monthAggregateYoutubeDataFormat($monthAggregateYoutubeData);
        $this->storeYoutubeData('month', $monthAggregateYoutubeToArray);
    }

    /**
     * Youtubeデータを保存
     *
     * @param array $youtubeToArray
     * @return void
     */
    private function storeYoutubeData(string $repository, array $youtubeToArray): void
    {
        $chunksData = array_chunk($youtubeToArray, $this->chunkSize);

        foreach ($chunksData as $chunkData) {
            switch ($repository) {
                case 'dwh':
                    $this->dwhDailyYoutubeRepository->bulkInsert($chunkData);
                    break;
                case 'daily':
                    $this->dailyYoutubeRepository->bulkInsert($chunkData);
                    break;
                case 'week':
                    $this->weekYoutubeRepository->bulkInsert($chunkData);
                    break;
                case 'month':
                    $this->monthYoutubeRepository->bulkInsert($chunkData);
                    break;
            }
        }
    }

    /**
     * 週別Youtube集計データを整形
     *
     * @param array $weekAggregateYoutubeData
     * @return array
     */
    private function weekAggregateYoutubeDataFormat(array $weekAggregateYoutubeData): array
    {
        return array_map(function ($weekAggregateYoutube) {
            return [
                'search_category_id' => $weekAggregateYoutube->search_category_id,
                'target_year' => $weekAggregateYoutube->target_year,
                'target_week' => $weekAggregateYoutube->target_week,
                'target_week_day' => $weekAggregateYoutube->target_week_day,
                'ranking' => $weekAggregateYoutube->ranking,
                'week_view_count' => $weekAggregateYoutube->week_view_count,
                'video_id' => $weekAggregateYoutube->video_id,
                'title' => $weekAggregateYoutube->title,
                'description' => $weekAggregateYoutube->description,
                'channel_id' => $weekAggregateYoutube->channel_id,
                'channel_name' => $weekAggregateYoutube->channel_name,
                'view_count' => $weekAggregateYoutube->view_count,
                'like_count' => $weekAggregateYoutube->like_count,
                'comment_count' => $weekAggregateYoutube->comment_count,
                'category_id' => $weekAggregateYoutube->category_id,
                'url' => $weekAggregateYoutube->url,
                'duration' => $weekAggregateYoutube->duration,
                'published_at' => $weekAggregateYoutube->published_at,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }, $weekAggregateYoutubeData);
    }

    /**
     * 月別Youtube集計データを整形
     *
     * @param array $monthAggregateYoutubeData
     * @return array
     */
    private function monthAggregateYoutubeDataFormat(array $monthAggregateYoutubeData): array
    {
        return array_map(function ($monthAggregateYoutube) {
            return [
                'search_category_id' => $monthAggregateYoutube->search_category_id,
                'target_year' => $monthAggregateYoutube->target_year,
                'target_month' => $monthAggregateYoutube->target_month,
                'ranking' => $monthAggregateYoutube->ranking,
                'month_view_count' => $monthAggregateYoutube->month_view_count,
                'video_id' => $monthAggregateYoutube->video_id,
                'title' => $monthAggregateYoutube->title,
                'description' => $monthAggregateYoutube->description,
                'channel_id' => $monthAggregateYoutube->channel_id,
                'channel_name' => $monthAggregateYoutube->channel_name,
                'view_count' => $monthAggregateYoutube->view_count,
                'like_count' => $monthAggregateYoutube->like_count,
                'comment_count' => $monthAggregateYoutube->comment_count,
                'category_id' => $monthAggregateYoutube->category_id,
                'url' => $monthAggregateYoutube->url,
                'duration' => $monthAggregateYoutube->duration,
                'published_at' => $monthAggregateYoutube->published_at,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }, $monthAggregateYoutubeData);
    }
}
