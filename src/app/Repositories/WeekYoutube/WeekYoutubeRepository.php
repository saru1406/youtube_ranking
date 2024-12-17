<?php

declare(strict_types=1);

namespace App\Repositories\WeekYoutube;

use App\Models\WeekYoutubeVideo;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class WeekYoutubeRepository implements WeekYoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function bulkInsert(array $data): void
    {
        WeekYoutubeVideo::insert($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        WeekYoutubeVideo::query()->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function fetchVideosByLastWeekByCategoryId(int $categoryId, array $with = [], ?int $limit = null): Collection
    {
        return WeekYoutubeVideo::with($with)
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
    public function fetchVideosByLastWeekByCategoryIdWithPagination(int $categoryId, array $with = [], int $perPage = 20): LengthAwarePaginator
    {
        return WeekYoutubeVideo::with($with)
            ->where('target_week', Carbon::today()->isoWeek)
            ->where('search_category_id', $categoryId)
            ->paginate($perPage);
    }
}
