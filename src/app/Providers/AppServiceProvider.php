<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\DlYoutube\DlYoutubeRepository;
use App\Repositories\DlYoutube\DlYoutubeRepositoryInterface;
use App\Repositories\DwhYoutube\DwhYoutubeRepository;
use App\Repositories\DwhYoutube\DwhYoutubeRepositoryInterface;
use App\Repositories\Youtube\YoutubeRepository;
use App\Repositories\Youtube\YoutubeRepositoryInterface;
use App\Usecase\Job\RunYoutubeJobUsecase;
use App\Usecase\Job\RunYoutubeJobUsecaseInterface;
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
        $this->app->bind(RunYoutubeJobUsecaseInterface::class, RunYoutubeJobUsecase::class);

        // DLYoutube
        $this->app->bind(DlYoutubeRepositoryInterface::class, DlYoutubeRepository::class);

        // DWHYoutube
        $this->app->bind(DwhYoutubeRepositoryInterface::class, DwhYoutubeRepository::class);

        // Youtube
        $this->app->bind(YoutubeRepositoryInterface::class, YoutubeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
