<?php

declare(strict_types=1);

namespace App\Repositories\DlYoutube;

use App\Models\DlYoutubeVideo;
use Illuminate\Support\Facades\DB;

class DlYoutubeRepository implements DlYoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function bulkInsert(array $data): void
    {
        DlYoutubeVideo::insert($data);
    }

    /**
     * {@inheritDoc}
     */
    public function existsByDateHour(string $dateHour): bool
    {
        return DB::table('dl_youtube_videos')
        ->where('created_at', 'like', "{$dateHour}%")
        ->exists();
    }

}
