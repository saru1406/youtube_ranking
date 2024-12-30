<?php

declare(strict_types=1);

namespace App\Usecase\Youtube\Month;

use App\Enums\Category\CategoryEnum;
use App\Repositories\MonthYoutube\MonthYoutubeRepositoryInterface;
use Illuminate\Support\Collection;

class MonthTrendYoutubeUsecase implements MonthTrendYoutubeUsecaseInterface
{
    public function __construct(
        private readonly MonthYoutubeRepositoryInterface $monthYoutubeRepository
    ) {}

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
    private function fetchVideosByCategory(): Collection
    {
        $categoryIds = CategoryEnum::toArray();
        $allVideosData = [];
        foreach ($categoryIds as $categoryId) {
            $videosData = $this->monthYoutubeRepository->fetchVideosByLastMonthByCategoryId(
                $categoryId,
                ['category:category_number,category_physical_name'],
                3
            );
            $allVideosData = array_merge($allVideosData, $videosData->toArray());
        }

        return collect($allVideosData)->groupBy('category.category_physical_name');
    }
}
