<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\AuditService;
use App\Services\ReminderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(ReminderService $reminderService, AuditService $auditService): void
    {
        Log::info('SendRemindersJob: Starting reminder generation and sending...');

        $generated = $reminderService->autoGenerateReminders();
        $sent = $reminderService->sendPendingReminders();

        $auditService->log(
            action: 'reminders_processed',
            description: "Auto-generated {$generated} reminders and sent {$sent} pending reminders.",
            modelType: null,
            modelId: null,
        );

        Log::info("SendRemindersJob: Completed. Generated: {$generated}, Sent: {$sent}");
    }
}
