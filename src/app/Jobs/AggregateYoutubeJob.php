<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Usecase\Job\RunAggregateYoutubeJobUsecaseInterface;
use DomainException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class AggregateYoutubeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly RunAggregateYoutubeJobUsecaseInterface $runAggregateYoutubeJobUsecase
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('AggregateYoutubeJob 開始');

            $this->runAggregateYoutubeJobUsecase->execute();

            Log::info('AggregateYoutubeJob 終了');
        } catch (DomainException $e) {
            Log::error($e->getMessage());
        }
    }
}
