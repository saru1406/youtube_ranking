<?php

declare(strict_types=1);

namespace App\Repositories\DlDailyYoutube;

use Illuminate\Support\Collection;

interface DlDailyYoutubeRepositoryInterface
{
    /**
     * DlDailyYoutubeにデータを一括保存
     *
     * @param array $data
     * @return void
     */
    public function bulkInsert(array $data): void;

    /**
     * ulidからDlDailyYoutubeを取得
     *
     * @param string $ulid
     * @return Collection
     */
    public function fetchDlDailyYoutubeByUlid(string $ulid): Collection;

    /**
     * 日付が保存されているか存在確認
     *
     * @param string $date
     * @return bool
     */
    public function existsByDate(string $date): bool;
}
