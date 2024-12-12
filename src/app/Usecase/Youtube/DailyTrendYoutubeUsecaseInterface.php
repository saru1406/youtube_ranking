<?php

declare(strict_types=1);

namespace App\Usecase\Youtube;

use Illuminate\Support\Collection;

interface DailyTrendYoutubeUsecaseInterface
{
    /**
     * Youtubeのデイリートレンドを取得
     *
     * @return Collection
     */
    public function execute(): Collection;
}
