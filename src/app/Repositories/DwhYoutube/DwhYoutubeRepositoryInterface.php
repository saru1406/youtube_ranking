<?php

declare(strict_types=1);

namespace App\Repositories\DwhYoutube;

use Illuminate\Support\Collection;

interface DwhYoutubeRepositoryInterface
{
    /**
     * DwhYoutubeにデータを一括保存
     *
     * @param array $data
     * @return void
     */
    public function bulkInsert(array $data): void;

    /**
     * DwhYoutubeから直近1時間の動画を取得
     *
     * @param int $categoryId
     * @param ?int $limit
     * @return Collection
     */
    public function fetchVideosByLastHourByCategory(int $categoryId, ?int $limit = null): Collection;
}
