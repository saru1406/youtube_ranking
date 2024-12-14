<?php

declare(strict_types=1);

namespace App\Repositories\DailyYoutube;

interface DailyYoutubeRepositoryInterface
{
    /**
     * DailyYoutubeにデータを一括保存
     *
     * @param array $data
     * @return void
     */
    public function bulkInsert(array $data): void;

    /**
     * DailyYoutubeデータをすべて削除
     *
     * @return void
     */
    public function delete(): void;
}
