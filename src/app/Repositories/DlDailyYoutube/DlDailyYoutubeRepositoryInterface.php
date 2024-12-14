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
     * 日付からDlDailyYoutubeを取得
     *
     * @param string $date
     * @return Collection
     */
    public function fetchDlDailyYoutubeByDate(string $date): Collection;

    /**
     * 日付が保存されているか存在確認
     * 
     * @param string $date
     * @return bool
     */
    public function existsByDate(string $date): bool;
}
