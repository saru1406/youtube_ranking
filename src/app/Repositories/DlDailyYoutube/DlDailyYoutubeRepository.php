<?php

declare(strict_types=1);

namespace App\Repositories\DlDailyYoutube;

use App\Models\DlDailyYoutubeVideo;
use Illuminate\Support\Collection;

class DlDailyYoutubeRepository implements DlDailyYoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function bulkInsert(array $data): void
    {
        DlDailyYoutubeVideo::insert($data);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchDlDailyYoutubeByDate(string $date): Collection
    {
        return DlDailyYoutubeVideo::whereDate('created_at', $date)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function existsByDate(string $date): bool
    {
        return DlDailyYoutubeVideo::whereDate('created_at', $date)->exists();
    }
}
