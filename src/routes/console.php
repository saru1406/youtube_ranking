<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command('app:hour-job-run')->cron('50 * * * *')->withoutOverlapping();
Schedule::command('app:aggregate-run')->dailyAt('23:50');
