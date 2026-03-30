<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TaskReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
    ) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('SCIManager — Rappel')
            ->icon('/assets/img/pwa-192.png')
            ->body($this->task->title)
            ->action('Voir', 'view_task')
            ->data(['url' => route('tasks.index'), 'task_id' => $this->task->id]);
    }
}
