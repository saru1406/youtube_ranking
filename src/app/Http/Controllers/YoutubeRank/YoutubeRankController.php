<?php

declare(strict_types=1);

namespace App\Http\Controllers\YoutubeRank;

use App\Http\Controllers\Controller;
use App\Usecase\Youtube\Daily\DailyTrendByCategoryYoutubeUsecaseInterface;
use App\Usecase\Youtube\Daily\DailyTrendYoutubeUsecaseInterface;
use App\Usecase\Youtube\Week\WeekTrendByCategoryYoutubeUsecaseInterface;
use App\Usecase\Youtube\Week\WeekTrendYoutubeUsecaseInterface;
use Inertia\Inertia;

class YoutubeRankController extends Controller
{
    public function __construct(
        private readonly DailyTrendYoutubeUsecaseInterface $dailyTrendYoutubeUsecase,
        private readonly DailyTrendByCategoryYoutubeUsecaseInterface $dailyTrendByCategoryYoutubeUsecase,
        private readonly WeekTrendYoutubeUsecaseInterface $weekTrendYoutubeUsecase,
        private readonly WeekTrendByCategoryYoutubeUsecaseInterface $weekTrendByCategoryYoutubeUsecase,
    ) {}

    public function dailyTrend()
    {
        $data = $this->dailyTrendYoutubeUsecase->execute();

        return Inertia::render('Welcome', [
            'trend_data' => $data,
        ]);
    }

    public function dailyTrendByCategory(string $categoryName)
    {
        $data = $this->dailyTrendByCategoryYoutubeUsecase->execute($categoryName);

        return Inertia::render('Trend/Category/Index', [
            'trend_data' => $data,
        ]);
    }

    public function weekTrend()
    {
        $data = $this->weekTrendYoutubeUsecase->execute();

        return Inertia::render('Week/Index', [
            'trend_data' => $data,
        ]);
    }

    public function weekTrendByCategory(string $categoryName)
    {
        $data = $this->weekTrendByCategoryYoutubeUsecase->execute($categoryName);

        return Inertia::render('Week/Category/Index', [
            'trend_data' => $data,
        ]);
    }
}
