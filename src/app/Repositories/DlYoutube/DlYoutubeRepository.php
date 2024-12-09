<?php

declare(strict_types=1);

namespace App\Repositories\DlYoutube;

use App\Models\DlYoutubeVideo;

class DlYoutubeRepository implements DlYoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function bulkInsert(array $categoryVideosData): void
    {
        DlYoutubeVideo::insert($categoryVideosData);
    }
}
