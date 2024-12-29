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
                    post_year,
                    post_week,
                    post_week_day,
                    target_year,
                    target_week,
                    target_week_day,
                    search_category_id,
                    video_id,
                    view_count,
                    category_id,
                    published_at,
                    created_at
                FROM
                    dwh_daily_youtube_videos
            ),
            YOUTUBE_NEXT_WEEK AS (
                SELECT
                    yw.video_id,
                    yw.search_category_id,
                    CASE
                        WHEN (yw.target_year = yw.post_year AND yw.target_week = yw.post_week)
                            AND yw.post_week_day >= 5
                            AND yw.target_week = WEEK(CONCAT(yw.target_year, "-12-31"), 3)
                            THEN 1
                        WHEN (yw.target_year = yw.post_year AND yw.target_week = yw.post_week)
                            AND yw.post_week_day >= 5
                            THEN yw.target_week + 1
                        ELSE yw.target_week
                    END AS adjusted_week,
                    CASE
                        WHEN (yw.target_year = yw.post_year AND yw.target_week = yw.post_week)
                            AND yw.post_week_day >= 5
                            AND yw.target_week = WEEK(CONCAT(yw.target_year, "-12-31"), 3)
                            THEN yw.target_year + 1
                        ELSE yw.target_year
                    END AS adjusted_year,
                    yw.view_count,
                    yw.created_at,
                    CASE
                        WHEN yw.target_year = yw.post_year AND yw.target_week = yw.post_week THEN 1
                        ELSE 0
                    END AS next_week_flag
                FROM
                    YOUTUBE_WEEK AS yw
            ),
            YOUTUBE_VIEW_COUNT AS (
                SELECT
                    video_id,
                    search_category_id,
                    adjusted_year AS target_year,
                    adjusted_week AS target_week,
                    CASE
                        WHEN MAX(next_week_flag) = 1 THEN MAX(view_count)
                        ELSE MAX(view_count) - MIN(view_count)
                    END AS week_view_count,
                    MAX(created_at) AS latest_date
                FROM
                    YOUTUBE_NEXT_WEEK
                GROUP BY
                    video_id,
                    search_category_id,
                    adjusted_year,
                    adjusted_week
            ),
            YOUTUBE_RANK AS (
                SELECT
                    video_id,
                    search_category_id,
                    target_year,
                    target_week,
                    week_view_count,
                    ROW_NUMBER() OVER (PARTITION BY target_year, target_week, search_category_id ORDER BY week_view_count DESC) AS ranking,
                    latest_date
                FROM
                    YOUTUBE_VIEW_COUNT
            )
            SELECT
                yr.target_year,
                yr.target_week,
                ddyv.target_week_day,
                ddyv.search_category_id,
                ddyv.video_id,
                ddyv.title,
                ddyv.description,
                ddyv.channel_id,
                ddyv.channel_name,
                ddyv.view_count,
                ddyv.like_count,
                ddyv.comment_count,
                ddyv.category_id,
                ddyv.url,
                ddyv.duration,
                ddyv.published_at,
                ddyv.created_at,
                yr.week_view_count,
                yr.ranking
            FROM
                dwh_daily_youtube_videos AS ddyv
            JOIN
                YOUTUBE_RANK AS yr
                ON ddyv.video_id = yr.video_id
                AND ddyv.search_category_id = yr.search_category_id
                AND ddyv.created_at = yr.latest_date
        ');
    }

    /**
     * {@inheritDoc}
     */
    public function fetchDwhYoutubeByMonthAggregate(): array
    {
        return DB::select('
            WITH YOUTUBE_MONTH AS (
                SELECT
                    post_year,
                    post_month,
                    post_day,
                    target_year,
                    target_month,
                    search_category_id,
                    video_id,
                    view_count,
                    category_id,
                    published_at,
                    created_at
                FROM
                    dwh_daily_youtube_videos
            ),
            YOUTUBE_NEXT_MONTH AS (
                SELECT
                    ym.video_id,
                    ym.search_category_id,
                    CASE
                        WHEN (ym.target_year = ym.post_year AND ym.target_month = ym.post_month)
                            AND ym.post_day > DAY(LAST_DAY(CONCAT(ym.target_year, "-", LPAD(ym.target_month, 2, "0"), "-01")) - INTERVAL 7 DAY)
                            AND ym.post_month = 12
                            THEN 1
                        WHEN (ym.target_year = ym.post_year AND ym.target_month = ym.post_month)
                            AND ym.post_day > DAY(LAST_DAY(CONCAT(ym.target_year, "-", LPAD(ym.target_month, 2, "0"), "-01")) - INTERVAL 7 DAY)
                            THEN ym.target_month + 1
                        ELSE ym.target_month
                    END AS adjusted_month,
                    CASE
                        WHEN (ym.target_year = ym.post_year AND ym.target_month = ym.post_month)
                            AND ym.post_day > DAY(LAST_DAY(CONCAT(ym.target_year, "-", LPAD(ym.target_month, 2, "0"), "-01")) - INTERVAL 7 DAY)
                            AND ym.post_month = 12
                            THEN ym.target_year + 1
                        ELSE ym.target_year
                    END AS adjusted_year,
                    ym.view_count,
                    ym.created_at,
                    CASE
                        WHEN ym.target_year = ym.post_year AND ym.target_month = ym.post_month THEN 1
                        ELSE 0
                    END AS next_month_flag
                FROM
                    YOUTUBE_MONTH AS ym
            ),
            YOUTUBE_VIEW_COUNT AS (
                SELECT
                    video_id,
                    search_category_id,
                    adjusted_year AS target_year,
                    adjusted_month AS target_month,
                    CASE
                        WHEN MAX(next_month_flag) = 1 THEN MAX(view_count)
                        ELSE MAX(view_count) - MIN(view_count)
                    END AS month_view_count,
                    MAX(created_at) AS latest_date
                FROM
                    YOUTUBE_NEXT_MONTH
                GROUP BY
                    video_id,
                    search_category_id,
                    adjusted_year,
                    adjusted_month
            ),
            YOUTUBE_RANK AS (
                SELECT
                    video_id,
                    search_category_id,
                    target_year,
                    target_month,
                    month_view_count,
                    ROW_NUMBER() OVER (PARTITION BY target_year, target_month, search_category_id ORDER BY month_view_count DESC) AS ranking,
                    latest_date
                FROM
                    YOUTUBE_VIEW_COUNT
            )
            SELECT
                yr.target_year,
                yr.target_month,
                ddyv.search_category_id,
                ddyv.video_id,
                ddyv.title,
                ddyv.description,
                ddyv.channel_id,
                ddyv.channel_name,
                ddyv.view_count,
                ddyv.like_count,
                ddyv.comment_count,
                ddyv.category_id,
                ddyv.url,
                ddyv.duration,
                ddyv.published_at,
                ddyv.created_at,
                yr.month_view_count,
                yr.ranking
            FROM
                dwh_daily_youtube_videos AS ddyv
            JOIN
                YOUTUBE_RANK AS yr
                ON ddyv.video_id = yr.video_id
                AND ddyv.search_category_id = yr.search_category_id
                AND ddyv.created_at = yr.latest_date
        ');
    }
}
