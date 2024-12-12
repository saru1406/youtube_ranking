<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\HourYoutubeJob;
use App\Usecase\Job\RunHourYoutubeJobUsecaseInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HourYoutubeJobCommand extends Command
{
    public function __construct(
        private readonly RunHourYoutubeJobUsecaseInterface $runHourYoutubeJobUsecase
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:hour-job-run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'job execute';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            Log::info('実行開始');

            DB::transaction(function () {
                HourYoutubeJob::dispatch($this->runHourYoutubeJobUsecase);
            });

            Log::info('実行完了');
        } catch (Exception $e) {
            Log::error(
                'error:'.$e->getMessage()
            );
        }
    }
}
