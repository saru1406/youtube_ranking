<?php

declare(strict_types=1);

namespace App\Repositories\DwhYoutube;

use Illuminate\Pagination\LengthAwarePaginator;
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
     * カテゴリID毎にDwhYoutubeから直近1時間の動画を取得
     *
     * @param int $categoryId
     * @param array $with
     * @param ?int $limit
     * @return Collection
     */
    public function fetchVideosByLastHourByCategoryId(int $categoryId, array $with = [], ?int $limit = null): Collection;

    /**
     * DWHYoutubeからvideo_idを全量取得
     *
     * @return Collection
     */
    public function fetchDwhYoutubeVideoIds(): Collection;

    /**
     * カテゴリID毎にDwhYoutubeから直近1時間の動画をページネーションで取得
     *
     * @param int $categoryId
     * @param array $with
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function fetchVideosByLastHourByCategoryIdWithPagination(int $categoryId, array $with = [], int $perPage = 20): LengthAwarePaginator;
}
