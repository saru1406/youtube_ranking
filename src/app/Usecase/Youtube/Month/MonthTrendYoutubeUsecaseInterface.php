<?php

declare(strict_types=1);

namespace App\Usecase\Youtube\Month;

use Illuminate\Support\Collection;

interface MonthTrendYoutubeUsecaseInterface
{
    /**
     * Youtubeの週間トレンドを取得
     *
     * @return Collection
     */
    public function execute(): Collection;
}
