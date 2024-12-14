<?php

declare(strict_types=1);

namespace App\Repositories\DwhDailyYoutube;

use App\Models\DwhDailyYoutubeVideo;
use Illuminate\Support\Facades\DB;

class DwhDailyYoutubeRepository implements DwhDailyYoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function bulkInsert(array $data): void
    {
        DwhDailyYoutubeVideo::insert($data);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchDwhYoutubeByDailyAggregate(): array
    {
        return DB::select('
            SELECT
                ddyv.video_id,
                DAILY_LATEST_DATE.target_date,
                DAILY_LATEST_DATE.daily_view_count ,
                ddyv.search_category_id ,
                ddyv.title ,
                ddyv.description ,
                ddyv.channel_id ,
                ddyv.channel_name ,
                ddyv.view_count ,
                ddyv.like_count ,
                ddyv.comment_count ,
                ddyv.category_id ,
                ddyv.url ,
                ddyv.duration ,
                ddyv.published_at ,
                ddyv.created_at
            FROM
                dwh_daily_youtube_videos as ddyv
            JOIN
                (
                    SELECT
                        video_id,
                        DATE(created_at) AS target_date,
                        MAX(created_at) AS latest_date,
                        MAX(view_count) - MIN(view_count) AS daily_view_count
                    FROM
                        dwh_daily_youtube_videos
                    GROUP BY
                        video_id ,
                        target_date
                ) AS DAILY_LATEST_DATE
                ON ddyv.video_id = DAILY_LATEST_DATE.video_id
                    AND ddyv.created_at = DAILY_LATEST_DATE.latest_date
        ');
    }
}
