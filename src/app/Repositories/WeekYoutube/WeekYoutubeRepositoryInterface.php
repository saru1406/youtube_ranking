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

    /**
     * カテゴリ毎に週集計した動画を取得
     *
     * @param int $categoryId
     * @param array $with
     * @param mixed $limit
     * @return \Illuminate\Support\Collection
     */
    public function fetchVideosByLastWeekByCategoryId(int $categoryId, array $with = [], ?int $limit = null): Collection;

    /**
     * カテゴリ毎に週集計した動画をページネーションで取得
     *
     * @param int $categoryId
     * @param array $with
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function fetchVideosByLastWeekByCategoryIdWithPagination(int $categoryId, array $with = [], int $perPage = 20): LengthAwarePaginator;
}
