<?php

declare(strict_types=1);

namespace App\Repositories\Youtube;

interface YoutubeRepositoryInterface
{
    /**
     * YoutubeのJSONデータを取得
     *
     * @param int $categoryId
     * @param mixed $pageToken
     * @return array
     */
    public function fetchYoutubeData(int $categoryId, ?string $pageToken = null): array;

    /**
     * videoIdからYoutubeのJSONデータ取得
     *
     * @param array $videoIds
     * @return string
     */
    public function fetchYoutubeDataByVideoId(string $videoIds): array;
}
