<?php

declare(strict_types=1);

namespace App\Repositories\MonthYoutube;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MonthYoutubeRepositoryInterface
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

    public function fetchVideosByLastMonthByCategoryId(int $categoryId, array $with = [], ?int $limit = null): Collection;

    public function fetchVideosByLastMonthByCategoryIdWithPagination(int $categoryId, array $with = [], int $perPage = 20): LengthAwarePaginator;
}
