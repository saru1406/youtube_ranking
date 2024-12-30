<?php

declare(strict_types=1);

namespace App\Usecase\Youtube\Month;

use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\MonthYoutube\MonthYoutubeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class MonthTrendByCategoryYoutubeUsecase implements MonthTrendByCategoryYoutubeUsecaseInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly MonthYoutubeRepositoryInterface $monthYoutubeRepository,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function execute(string $categoryName): LengthAwarePaginator
    {
        $category = $this->categoryRepository->firstCategoryByCategoryName($categoryName);

        return $this->monthYoutubeRepository->fetchVideosByLastMonthByCategoryIdWithPagination($category->category_number);
    }
}
