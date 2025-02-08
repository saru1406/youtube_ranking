<?php

declare(strict_types=1);

namespace App\Repositories\DlYoutube;

use App\Models\DlYoutubeVideo;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
    public function existsByDateHour(Carbon $dateHour): bool
    {
        return DB::table('dl_youtube_videos')
            ->where('created_at', $dateHour)
            ->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function fetchDlYoutubeByUlid(string $ulid): Collection
    {
        return DlYoutubeVideo::where('ulid', $ulid)->get();
    }
}
