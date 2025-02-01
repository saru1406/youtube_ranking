<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\AggregateYoutubeJob;
use App\Usecase\Job\RunAggregateYoutubeJobUsecaseInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;

class AggregateYoutubeJobCommand extends Command
{
    public function __construct(
        private readonly RunAggregateYoutubeJobUsecaseInterface $runAggregateYoutubeJobUsecase
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:aggregate-run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            Log::info('実行開始');

            DB::transaction(function () {
                AggregateYoutubeJob::dispatch($this->runAggregateYoutubeJobUsecase);
            });

            Log::info('実行完了');
        } catch (Exception $e) {
            Log::error(
                'error:'.$e->getMessage()
            );
        }
    }
}
