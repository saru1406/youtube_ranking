<?php

declare(strict_types=1);

namespace App\Repositories\DlYoutube;

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
}
