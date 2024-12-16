<?php

declare(strict_types=1);

namespace App\Usecase\Youtube\Week;

use Illuminate\Support\Collection;

interface WeekTrendYoutubeUsecaseInterface
{
    /**
     * Youtubeの週間トレンドを取得
     *
     * @return Collection
     */
    public function execute(): Collection;
}
