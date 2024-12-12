<?php

declare(strict_types=1);

namespace App\Repositories\DailyYoutube;

interface DailyYoutubeRepositoryInterface
{
    /**
     * DlYoutubeにデータを一括保存
     *
     * @param array $data
     * @return void
     */
    public function bulkInsert(array $data): void;
}
