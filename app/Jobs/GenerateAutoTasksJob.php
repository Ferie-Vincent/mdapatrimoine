<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\AuditService;
use App\Services\TaskService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAutoTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(TaskService $taskService): void
    {
        Log::info('GenerateAutoTasksJob: Starting auto-task generation...');

        $generated = $taskService->generateAutoTasks();
        $archived = $taskService->archiveOldTasks(7);

        AuditService::log('auto_tasks_processed', null, [
            'generated' => $generated,
            'archived' => $archived,
        ]);

        Log::info("GenerateAutoTasksJob: Completed. Generated: {$generated}, Archived: {$archived}");
    }
}
