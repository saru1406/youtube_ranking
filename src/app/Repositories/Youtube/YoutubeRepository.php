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
        $query = [
            'part' => 'snippet,statistics',
            'chart' => 'mostPopular',
            'regionCode' => 'JP',
            'videoCategoryId' => $categoryId,
            'maxResults' => 50,
            'key' => config('apiKey.google_api_key'),
        ];
        if ($pageToken) {
            $query['pageToken'] = $pageToken;
        }

        $response = Http::get('https://www.googleapis.com/youtube/v3/videos', $query);

        if ($response->successful()) {
            // Log::info($response->json());

            return $response->json();
        }

        throw new \Exception('YouTube API request failed: '.$response->status());
    }
}
