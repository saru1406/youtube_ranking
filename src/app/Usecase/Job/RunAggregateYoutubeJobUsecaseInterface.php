<?php

declare(strict_types=1);

namespace App\Usecase\Job;

interface RunAggregateYoutubeJobUsecaseInterface
{
    /**
     * Youtubeジョブ実行
     *
     * @return void
     */
    public function execute(): void;
}
