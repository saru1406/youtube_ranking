<?php

declare(strict_types=1);

namespace App\Usecase\Youtube\Week;

use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\WeekYoutube\WeekYoutubeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class WeekTrendByCategoryYoutubeUsecase implements WeekTrendByCategoryYoutubeUsecaseInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly WeekYoutubeRepositoryInterface $weekYoutubeRepository,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function execute(string $categoryName): LengthAwarePaginator
    {
        $category = $this->categoryRepository->firstCategoryByCategoryName($categoryName);

        return $this->weekYoutubeRepository->fetchVideosByLastWeekByCategoryIdWithPagination($category->category_number);
    }
}
