<?php

declare(strict_types=1);

namespace App\Usecase\Youtube\Daily;

use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\DwhYoutube\DwhYoutubeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class DailyTrendByCategoryYoutubeUsecase implements DailyTrendByCategoryYoutubeUsecaseInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly DwhYoutubeRepositoryInterface $dwhYoutubeRepository
    ) {}

    /**
     * {@inheritDoc}
     */
    public function execute(string $categoryName): LengthAwarePaginator
    {
        $category = $this->categoryRepository->firstCategoryByCategoryName($categoryName);

        return $this->dwhYoutubeRepository->fetchVideosByLastHourByCategoryIdWithPagination($category->category_number);
    }
}
