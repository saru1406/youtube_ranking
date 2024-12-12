<?php

declare(strict_types=1);

namespace App\Repositories\Category;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    /**
     * カテゴリ名からカテゴリを取得
     *
     * @param string $categoryName
     * @return Category
     */
    public function firstCategoryByCategoryName(string $categoryName): Category;
}
