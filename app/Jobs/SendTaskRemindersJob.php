<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use App\Services\AuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTaskRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $tasks = Task::where('reminder_at', '<=', now())
            ->where('reminder_sent', false)
            ->whereIn('status', ['a_faire', 'en_cours'])
            ->with('user')
            ->get();

        $sent = 0;

        foreach ($tasks as $task) {
            if ($task->user) {
                $task->user->notify(new TaskReminderNotification($task));
                $task->update(['reminder_sent' => true]);
                $sent++;
            }
        }

        if ($sent > 0) {
            AuditService::log('task_reminders_sent', null, ['count' => $sent]);
        }

        Log::info("SendTaskRemindersJob: {$sent} reminder(s) sent out of {$tasks->count()} due task(s).");
    }
}
