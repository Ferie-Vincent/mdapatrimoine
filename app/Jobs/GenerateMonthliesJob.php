<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\AuditService;
use App\Services\MonthlyGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMonthliesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(MonthlyGenerationService $monthlyGenerationService, AuditService $auditService): void
    {
        Log::info('GenerateMonthliesJob: Starting monthly generation...');

        $generated = $monthlyGenerationService->generateAllPending();
        $penalties = $monthlyGenerationService->applyPenalties();

        $auditService->log(
            action: 'monthlies_generated',
            description: "Generated {$generated} monthly entries and applied {$penalties} penalties.",
            modelType: null,
            modelId: null,
        );

        Log::info("GenerateMonthliesJob: Completed. Generated: {$generated}, Penalties: {$penalties}");
    }
}
