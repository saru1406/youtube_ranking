<?php

declare(strict_types=1);

namespace App\Usecase\Youtube;

use App\Enums\Category\CategoryEnum;
use App\Repositories\DwhYoutube\DwhYoutubeRepositoryInterface;
use Illuminate\Support\Collection;

class DailyTrendYoutubeUsecase implements DailyTrendYoutubeUsecaseInterface
{
    public function __construct(private readonly DwhYoutubeRepositoryInterface $dwhYoutubeRepository) {}

    /**
     * {@inheritDoc}
     */
    public function execute(): Collection
    {
        return $this->fetchVideosByCategory();
    }

    private function fetchVideosByCategory()
    {
        $categoryIds = CategoryEnum::toArray();
        $allVideosData = [];
        foreach ($categoryIds as $categoryId) {
            $videosData = $this->dwhYoutubeRepository->fetchVideosByLastHourByCategory($categoryId, 3);
            $allVideosData = array_merge($allVideosData, $videosData->toArray());
        }

        return collect($allVideosData)->groupBy('search_category_id');
    }
}
