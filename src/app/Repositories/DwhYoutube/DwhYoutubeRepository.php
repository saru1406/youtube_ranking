<?php

declare(strict_types=1);

namespace App\Repositories\DwhYoutube;

use App\Models\DwhYoutubeVideo;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
}
