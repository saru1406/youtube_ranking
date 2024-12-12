<?php

namespace Database\Seeders;

use App\Enums\Category\CategoryEnum;
use App\Enums\Type\TypeEnum;
use App\Models\Category;
use App\Models\Project;
use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (CategoryEnum::cases() as $category) {
            Category::create([
                'category_number' => $category->value,
                'category_name' => $category->name,
                'category_physical_name' => $this->getPhysicalName($category),
            ]);
        }
    }

    /**
     * 任意の物理名を返す
     *
     * @param CategoryEnum $category
     * @return string
     */
    private function getPhysicalName(CategoryEnum $category): string
    {
        return match ($category) {
            CategoryEnum::ALL => 'generals',
            CategoryEnum::VIDEO => 'video-productions',
            CategoryEnum::MUSIC => 'musics',
            CategoryEnum::SPORTS => 'sports',
            CategoryEnum::GAME => 'games',
            CategoryEnum::ENTERTAINMENT => 'entertainments',
            CategoryEnum::NEWS => 'news',
            CategoryEnum::HOW_TO => 'how-to',
        };
    }
}
