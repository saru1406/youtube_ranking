<?php

declare(strict_types=1);

namespace App\Repositories\DwhYoutube;

use App\Models\DwhYoutubeVideo;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DwhYoutubeRepository implements DwhYoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function bulkInsert(array $data): void
    {
        DwhYoutubeVideo::insert($data);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchVideosByLastHourByCategoryId(int $categoryId, array $with = [], ?int $limit = null): Collection
    {
        return DwhYoutubeVideo::with($with)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->where('search_category_id', $categoryId)
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function fetchVideosByLastHourByCategoryIdWithPagination(int $categoryId, array $with = [], int $perPage = 20): LengthAwarePaginator
    {
        return DwhYoutubeVideo::with($with)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->where('search_category_id', $categoryId)
            ->paginate($perPage);
    }

    public function fetchDwhYoutubeByDailyAggregate(): array
    {
        return DB::select('
            SELECT
                dyv.video_id,
                DAYLIY_LATEST_DATE.target_date,
                DAYLIY_LATEST_DATE.daily_view_count ,
                dyv.search_category_id ,
                dyv.title ,
                dyv.description ,
                dyv.channel_id ,
                dyv.channel_name ,
                dyv.view_count ,
                dyv.like_count ,
                dyv.comment_count ,
                dyv.category_id ,
                dyv.url ,
                dyv.duration ,
                dyv.published_at ,
                dyv.created_at
            FROM
                dwh_youtube_videos as dyv
            JOIN
                (
                    SELECT
                        video_id,
                        DATE(created_at) AS target_date,
                        MAX(created_at) AS latest_date,
                        MAX(view_count) - MIN(view_count) AS daily_view_count
                    FROM
                        dwh_youtube_videos
                    GROUP BY
                        video_id ,
                        target_date
                ) AS DAYLIY_LATEST_DATE
                ON dyv.video_id = DAYLIY_LATEST_DATE.video_id
                    AND dyv.created_at = DAYLIY_LATEST_DATE.latest_date
        ');
    }
}
