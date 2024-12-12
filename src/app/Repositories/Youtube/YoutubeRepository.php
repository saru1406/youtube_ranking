<?php

declare(strict_types=1);

namespace App\Repositories\Youtube;

use Http;
use Illuminate\Support\Facades\Log;

class YoutubeRepository implements YoutubeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function fetchYoutubeData(int $categoryId, ?string $pageToken = null): array
    {
        Log::info('Youtube API 取得開始', ['categoryId' => $categoryId, 'pageToken' => $pageToken]);

        $query = [
            'part' => 'snippet,statistics,contentDetails',
            'chart' => 'mostPopular',
            'regionCode' => 'JP',
            'maxResults' => 50,
            'key' => config('apiKey.google_api_key'),
        ];

        if ($categoryId !== 0) {
            $query['videoCategoryId'] = $categoryId;
        }

        if ($pageToken) {
            $query['pageToken'] = $pageToken;
        }

        Log::info('query', $query);

        $response = Http::get('https://www.googleapis.com/youtube/v3/videos', $query);

        if ($response->successful()) {
            Log::info('Youtube API 取得完了', ['categoryId' => $categoryId, 'pageToken' => $pageToken]);

            return $response->json();
        }

        throw new \Exception('YouTube API request failed: '.$response->status());
    }
}
