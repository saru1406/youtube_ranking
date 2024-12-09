<?php

declare(strict_types=1);

namespace App\Repositories\DlYoutube;

interface DlYoutubeRepositoryInterface
{
    /**
     * カテゴリごとに取得したデータを一括保存
     *
     * @param array $categoryVideosData
     * @return void
     */
    public function bulkInsert(array $categoryVideosData): void;
}
