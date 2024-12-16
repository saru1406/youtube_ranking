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

    /**
     * {@inheritDoc}
     */
    public function fetchDwhYoutubeByWeekAggregate(): array
    {
        return DB::select('
        WITH YOUTUBE_WEEK AS (
            SELECT
                video_id,
                search_category_id,
                view_count,
                created_at,
                DATE(created_at) AS target_day,
                WEEK(published_at, 1) AS week,
                WEEK(created_at, 1) AS target_week,
                WEEKDAY(created_at) AS target_week_day,
                WEEKDAY(published_at) AS week_day
            FROM
                dwh_daily_youtube_videos
        ),
        NEXT_WEEK AS (
            SELECT
                yw.video_id,
                yw.search_category_id,
                CASE
                    WHEN yw.week_day >= 4 THEN yw.target_week + 1
                    ELSE yw.target_week
                END AS adjusted_week,
                yw.view_count,
                yw.created_at,
                IF(yw.week_day >= 4, 1, 0) AS NEXT_WEEK_FLAG
            FROM
                YOUTUBE_WEEK AS yw
            WHERE
                yw.target_week = yw.week
        ),
        UNION_YOUTUBE AS (
            SELECT
                yw.video_id,
                yw.search_category_id,
                yw.target_week,
                yw.view_count,
                yw.created_at
            FROM
                YOUTUBE_WEEK AS yw
            WHERE
                yw.target_week != yw.week
            UNION ALL
            SELECT
                nw.video_id,
                nw.search_category_id,
                nw.adjusted_week,
                nw.view_count,
                nw.created_at
            FROM
                NEXT_WEEK AS nw
            WHERE
                nw.NEXT_WEEK_FLAG = 1
        ),
        NEXT_WEEK_VIEW_COUNT AS (
            SELECT
                video_id,
                search_category_id,
                adjusted_week,
                MAX(view_count) AS week_view_count,
                MAX(created_at) AS latest_date
            FROM
                NEXT_WEEK
            WHERE
                NEXT_WEEK_FLAG = 0
            GROUP BY
                video_id,
                search_category_id,
                adjusted_week
        ),
        WEEK_VIEW_COUNT AS (
            SELECT
                video_id,
                search_category_id,
                target_week,
                MAX(view_count) - MIN(view_count) AS week_view_count,
                MAX(created_at) AS latest_date
            FROM
                UNION_YOUTUBE
            GROUP BY
                video_id,
                search_category_id,
                target_week
        ),
        UNION_WEEK_VIEW AS (
            SELECT
                *
            FROM
                WEEK_VIEW_COUNT
            UNION ALL
            SELECT
                *
            FROM
                NEXT_WEEK_VIEW_COUNT
        ),
        RANK_YOUTUBE AS (
            SELECT
                video_id,
                search_category_id,
                latest_date,
                target_week,
                week_view_count,
                ROW_NUMBER() OVER (PARTITION BY target_week, search_category_id ORDER BY week_view_count DESC) AS ranking
            FROM
                UNION_WEEK_VIEW
        )
        SELECT
            ddyv.video_id,
            ry.week_view_count,
            ry.ranking,
            ry.target_week,
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
            dwh_daily_youtube_videos AS ddyv
        JOIN
            RANK_YOUTUBE AS ry
            ON ddyv.video_id = ry.video_id
            AND ddyv.search_category_id = ry.search_category_id
            AND ddyv.created_at = ry.latest_date
        ');
    }
}
