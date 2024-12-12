<?php

declare(strict_types=1);

namespace App\Repositories\DailyYoutube;

use App\Models\DailyYoutubeVideo;

class DailyYoutubeRepository implements DailyYoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function bulkInsert(array $data): void
    {
        DailyYoutubeVideo::insert($data);
    }
}
