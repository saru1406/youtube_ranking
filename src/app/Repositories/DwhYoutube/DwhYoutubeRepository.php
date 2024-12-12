<?php

declare(strict_types=1);

namespace App\Repositories\DwhYoutube;

use App\Models\DwhYoutubeVideo;
use Carbon\Carbon;
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
    public function fetchVideosByLastHourByCategory(int $categoryId, ?int $limit = null): Collection
    {
        return DwhYoutubeVideo::where('created_at', '>=', Carbon::now()->subHour())->where('search_category_id', $categoryId)->limit($limit)->get();
    }
}
