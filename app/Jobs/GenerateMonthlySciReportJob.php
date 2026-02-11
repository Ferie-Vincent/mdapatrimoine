<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Sci;
use App\Services\DocumentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMonthlySciReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Sci $sci,
        public readonly string $month,
    ) {
        //
    }

    public function handle(DocumentService $documentService): void
    {
        Log::info("GenerateMonthlySciReportJob: Generating report for SCI [{$this->sci->name}] for month {$this->month}...");

        $documentService->generateMonthlyReport($this->sci, $this->month);

        Log::info("GenerateMonthlySciReportJob: Report generated for SCI [{$this->sci->name}] for month {$this->month}.");
    }
}
