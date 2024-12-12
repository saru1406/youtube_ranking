<?php

declare(strict_types=1);

namespace App\Http\Controllers\YoutubeRank;

use App\Http\Controllers\Controller;
use App\Usecase\Youtube\DailyTrendByCategoryYoutubeUsecaseInterface;
use App\Usecase\Youtube\DailyTrendYoutubeUsecaseInterface;
use Inertia\Inertia;

class YoutubeRankController extends Controller
{
    public function __construct(
        private readonly DailyTrendYoutubeUsecaseInterface $dailyTrendYoutubeUsecase,
        private readonly DailyTrendByCategoryYoutubeUsecaseInterface $dailyTrendByCategoryYoutubeUsecase,
    ) {}

    public function dailyTrend()
    {
        $data = $this->dailyTrendYoutubeUsecase->execute();

        return Inertia::render('Welcome', [
            'trend_data' => $data,
        ]);
    }

    public function DailyTrendByCategory(string $categoryName)
    {
        $data = $this->dailyTrendByCategoryYoutubeUsecase->execute($categoryName);

        return Inertia::render('Trend/Category/Index', [
            'trend_data' => $data,
        ]);
    }
}
