<?php

namespace App\Jobs;

use App\Usecase\Job\RunYoutubeJobUsecaseInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class YoutubeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly RunYoutubeJobUsecaseInterface $runYoutubeJobUsecase
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->runYoutubeJobUsecase->execute();
    }
}
