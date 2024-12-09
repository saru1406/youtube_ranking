<?php

declare(strict_types=1);

namespace App\Repositories\DwhYoutube;

interface DwhYoutubeRepositoryInterface
{
    /**
     * DwhYoutubeにデータを一括保存
     *
     * @param array $data
     * @return void
     */
    public function bulkInsert(array $data): void;
}
