<?php

declare(strict_types=1);

namespace App\Repositories\WeekYoutube;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface WeekYoutubeRepositoryInterface
{
    /**
     * WeekYoutubeにデータを一括保存
     *
     * @param array $data
     * @return void
     */
    public function bulkInsert(array $data): void;

    /**
     * WeekYoutubeデータをすべて削除
     *
     * @return void
     */
    public function delete(): void;

    public function fetchVideosByLastWeekByCategoryId(int $categoryId, array $with = [], ?int $limit = null): Collection;

    public function fetchVideosByLastWeekByCategoryIdWithPagination(int $categoryId, array $with = [], int $perPage = 20): LengthAwarePaginator;
}
