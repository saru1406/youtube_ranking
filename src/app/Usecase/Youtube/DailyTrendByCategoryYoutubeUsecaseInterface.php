<?php

declare(strict_types=1);

namespace App\Usecase\Youtube;

use Illuminate\Pagination\LengthAwarePaginator;

interface DailyTrendByCategoryYoutubeUsecaseInterface
{
    /**
     * カテゴリ名ごとのYoutubeのデイリートレンドを取得
     *
     * @param string $categoryName
     * @return LengthAwarePaginator
     */
    public function execute(string $categoryName): LengthAwarePaginator;
}
