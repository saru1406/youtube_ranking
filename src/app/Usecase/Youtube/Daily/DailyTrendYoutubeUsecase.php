<?php

declare(strict_types=1);

namespace App\Usecase\Youtube\Daily;

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

    /**
     * Youtubeデータを取得しカテゴリIdごとにグループ化
     *
     * @return Collection
     */
    private function fetchVideosByCategory()
    {
        $categoryIds = CategoryEnum::toArray();
        $allVideosData = [];
        foreach ($categoryIds as $categoryId) {
            $videosData = $this->dwhYoutubeRepository->fetchVideosByLastHourByCategoryId(
                $categoryId,
                ['category:category_number,category_physical_name'],
                3
            );
            $allVideosData = array_merge($allVideosData, $videosData->toArray());
        }

        return collect($allVideosData)->groupBy('category.category_physical_name');
    }
}
