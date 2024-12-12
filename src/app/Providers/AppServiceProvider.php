<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\DailyYoutube\DailyYoutubeRepository;
use App\Repositories\DailyYoutube\DailyYoutubeRepositoryInterface;
use App\Repositories\DlYoutube\DlYoutubeRepository;
use App\Repositories\DlYoutube\DlYoutubeRepositoryInterface;
use App\Repositories\DwhYoutube\DwhYoutubeRepository;
use App\Repositories\DwhYoutube\DwhYoutubeRepositoryInterface;
use App\Repositories\Youtube\YoutubeRepository;
use App\Repositories\Youtube\YoutubeRepositoryInterface;
use App\Usecase\Job\RunAggregateYoutubeJobUsecase;
use App\Usecase\Job\RunAggregateYoutubeJobUsecaseInterface;
use App\Usecase\Job\RunHourYoutubeJobUsecase;
use App\Usecase\Job\RunHourYoutubeJobUsecaseInterface;
use App\Usecase\Youtube\DailyTrendByCategoryYoutubeUsecase;
use App\Usecase\Youtube\DailyTrendByCategoryYoutubeUsecaseInterface;
use App\Usecase\Youtube\DailyTrendYoutubeUsecase;
use App\Usecase\Youtube\DailyTrendYoutubeUsecaseInterface;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // YoutubeJob
        $this->app->bind(RunAggregateYoutubeJobUsecaseInterface::class, RunAggregateYoutubeJobUsecase::class);
        $this->app->bind(RunHourYoutubeJobUsecaseInterface::class, RunHourYoutubeJobUsecase::class);

        // DLYoutubeRepository
        $this->app->bind(DlYoutubeRepositoryInterface::class, DlYoutubeRepository::class);

        // DWHYoutubeRepository
        $this->app->bind(DwhYoutubeRepositoryInterface::class, DwhYoutubeRepository::class);

        // DailyYoutubeRepository
        $this->app->bind(DailyYoutubeRepositoryInterface::class, DailyYoutubeRepository::class);

        // YoutubeRepository
        $this->app->bind(YoutubeRepositoryInterface::class, YoutubeRepository::class);

        // YoutubeUsecase
        $this->app->bind(DailyTrendYoutubeUsecaseInterface::class, DailyTrendYoutubeUsecase::class);
        $this->app->bind(DailyTrendByCategoryYoutubeUsecaseInterface::class, DailyTrendByCategoryYoutubeUsecase::class);

        // CategoryRepository
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
