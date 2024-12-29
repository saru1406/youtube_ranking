<?php

declare(strict_types=1);

namespace App\Repositories\MonthYoutube;

use App\Models\MonthYoutubeVideo;
use App\Models\WeekYoutubeVideo;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MonthYoutubeRepository implements MonthYoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function bulkInsert(array $data): void
    {
        MonthYoutubeVideo::insert($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        MonthYoutubeVideo::query()->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function fetchVideosByLastMonthByCategoryId(int $categoryId, array $with = [], ?int $limit = null): Collection
    {
        return MonthYoutubeVideo::with($with)
            ->where('target_week', Carbon::today()->isoWeek)
            // 本番用
            // ->where('target_week', Carbon::today()->isoWeek - 1)
            ->where('search_category_id', $categoryId)
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function fetchVideosByLastMonthByCategoryIdWithPagination(int $categoryId, array $with = [], int $perPage = 20): LengthAwarePaginator
    {
        return MonthYoutubeVideo::with($with)
            ->where('target_week', Carbon::today()->isoWeek)
            ->where('search_category_id', $categoryId)
            ->paginate($perPage);
    }
}
