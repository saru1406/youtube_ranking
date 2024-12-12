<?php

declare(strict_types=1);

namespace App\Repositories\Category;

use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function firstCategoryByCategoryName(string $categoryName): Category
    {
        return Category::where('category_physical_name', $categoryName)->first();
    }
}
