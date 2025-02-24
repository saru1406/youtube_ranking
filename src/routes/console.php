<?php

declare(strict_types=1);

Schedule::command('app:hour-job-run')->cron('50 * * * *')->withoutOverlapping();
Schedule::command('app:aggregate-run')->dailyAt('23:55');
