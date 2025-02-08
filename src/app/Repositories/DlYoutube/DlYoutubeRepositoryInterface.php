<?php

declare(strict_types=1);

namespace App\Repositories\DlYoutube;

use Illuminate\Support\Collection;

interface DlYoutubeRepositoryInterface
{
    /**
     * DlYoutubeにデータを一括保存
     *
     * @param array $data
     * @return void
     */
    public function bulkInsert(array $data): void;

    /**
     * 日時が保存されているか存在確認
     *
     * @param string $dateHour
     * @return bool
     */
    public function existsByDateHour(string $dateHour): bool;

    /**
     * ulidを元にDlYoutubeデータを取得
     *
     * @param string $ulid
     * @return Collection
     */
    public function fetchDlYoutubeByUlid(string $ulid): Collection;
}
