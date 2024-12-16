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
    public function fetchDlDailyYoutubeByUlid(string $ulid): Collection
    {
        return DlDailyYoutubeVideo::where('ulid', $ulid)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function existsByDate(string $date): bool
    {
        return DlDailyYoutubeVideo::whereDate('created_at', $date)->exists();
    }
}
