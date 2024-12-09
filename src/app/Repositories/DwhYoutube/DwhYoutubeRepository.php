<?php

declare(strict_types=1);

namespace App\Repositories\DwhYoutube;

use App\Models\DwhYoutubeVideo;

class DwhYoutubeRepository implements DwhYoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function bulkInsert(array $data): void
    {
        DwhYoutubeVideo::insert($data);
    }
}
