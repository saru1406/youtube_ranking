<?php

declare(strict_types=1);

namespace App\Usecase\Youtube\Week;

use Illuminate\Pagination\LengthAwarePaginator;

interface WeekTrendByCategoryYoutubeUsecaseInterface
{
    /**
     * カテゴリ名ごとのYoutubeのデイリートレンドを取得
     *
     * @param string $categoryName
     * @return LengthAwarePaginator
     */
    public function execute(string $categoryName): LengthAwarePaginator;
}
