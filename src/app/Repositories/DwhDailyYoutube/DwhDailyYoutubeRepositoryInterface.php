<?php

declare(strict_types=1);

namespace App\Repositories\DwhDailyYoutube;

interface DwhDailyYoutubeRepositoryInterface
{
    /**
     * DlDailyYoutubeにデータを一括保存
     *
     * @param array $data
     * @return void
     */
    public function bulkInsert(array $data): void;

    public function fetchDwhYoutubeByDailyAggregate(): array;

    public function fetchDwhYoutubeByWeekAggregate(): array;

    public function fetchDwhYoutubeByMonthAggregate(): array;
}
