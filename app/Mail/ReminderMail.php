<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Reminder $reminder,
        public readonly string $tenantName,
        public readonly string $propertyRef,
        public readonly string $month,
        public readonly float $remainingAmount,
        public readonly int $level,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            1 => "Rappel de loyer - {$this->month}",
            2 => "Relance de loyer impaye - {$this->month}",
            3 => "MISE EN DEMEURE - Loyer impaye {$this->month}",
        ];

        return new Envelope(
            subject: $subjects[$this->level] ?? $subjects[1],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder',
        );
    }
}
