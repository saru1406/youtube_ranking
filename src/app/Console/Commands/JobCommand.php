<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\YoutubeJob;
use App\Usecase\Job\RunYoutubeJobUsecaseInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class JobCommand extends Command
{
    public function __construct(
        private readonly RunYoutubeJobUsecaseInterface $runYoutubeJobUsecase
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:job-run';

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
        Log::info('実行開始');
        YoutubeJob::dispatch($this->runYoutubeJobUsecase);
        Log::info('実行完了');
    }
}
