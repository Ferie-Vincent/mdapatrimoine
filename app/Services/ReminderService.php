<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\ReminderMail;
use App\Models\LeaseMonthly;
use App\Models\Reminder;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReminderService
{
    public function __construct(
        private readonly TwilioService $twilioService,
    ) {}

    /**
     * Get reminder levels configuration from settings.
     */
    private function getLevels(): array
    {
        return [
            1 => (int) Setting::get('reminder_level1_days', 3),
            2 => (int) Setting::get('reminder_level2_days', 7),
            3 => (int) Setting::get('reminder_level3_days', 15),
        ];
    }

    /**
     * Create a reminder for a given monthly.
     */
    public function createReminder(LeaseMonthly $monthly, string $channel, string $message, int $level = 1): Reminder
    {
        $reminder = Reminder::create([
            'sci_id'           => $monthly->sci_id,
            'lease_monthly_id' => $monthly->id,
            'channel'          => $channel,
            'message'          => $message,
            'level'            => $level,
            'status'           => 'brouillon',
            'sent_by'          => auth()->id(),
        ]);

        AuditService::log(
            'created',
            Reminder::class,
            $reminder->id,
            [
                'lease_monthly_id' => $monthly->id,
                'channel'          => $channel,
                'level'            => $level,
            ],
            $monthly->sci_id
        );

        return $reminder;
    }

    /**
     * Send a single reminder via the appropriate channel.
     */
    public function sendReminder(Reminder $reminder): bool
    {
        $reminder->loadMissing(['leaseMonthly.lease.tenant', 'leaseMonthly.lease.property']);

        $monthly  = $reminder->leaseMonthly;
        $tenant   = $monthly->lease->tenant ?? null;

        if (!$tenant) {
            $reminder->update([
                'status'        => 'echec',
                'error_message' => 'Locataire introuvable.',
                'sent_at'       => now(),
            ]);
            return false;
        }

        $channel = $reminder->channel;

        return match ($channel) {
            'whatsapp' => $this->sendViaWhatsApp($reminder, $tenant),
            'sms'      => $this->sendViaSms($reminder, $tenant),
            'email'    => $this->sendViaEmail($reminder, $tenant),
            default    => $this->markAsSent($reminder), // courrier: just mark as sent
        };
    }

    /**
     * Send all pending (brouillon) reminders via their respective channels.
     *
     * @return int Number of reminders sent.
     */
    public function sendPendingReminders(): int
    {
        $count = 0;

        $pendingReminders = Reminder::where('status', 'brouillon')
            ->with(['leaseMonthly.lease.tenant', 'leaseMonthly.lease.property'])
            ->get();

        foreach ($pendingReminders as $reminder) {
            if ($this->sendReminder($reminder)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Auto-generate reminders for overdue unpaid monthlies with 3 progressive levels.
     *
     * Level 1 (J+3): Courtois
     * Level 2 (J+7): Ferme
     * Level 3 (J+15): Mise en demeure
     *
     * @return int Number of reminders generated.
     */
    public function autoGenerateReminders(): int
    {
        $count = 0;
        $levels = $this->getLevels();

        $overdueMonthlies = LeaseMonthly::whereIn('status', ['impaye', 'partiel', 'en_retard'])
            ->where('remaining_amount', '>', 0)
            ->with(['lease.tenant', 'lease.property'])
            ->get();

        foreach ($overdueMonthlies as $monthly) {
            $daysOverdue = Carbon::parse($monthly->due_date)->diffInDays(Carbon::now(), false);

            if ($daysOverdue < $levels[1]) {
                continue; // Not yet overdue enough for level 1
            }

            $tenant   = $monthly->lease->tenant ?? null;
            $property = $monthly->lease->property ?? null;

            if (!$tenant) {
                continue;
            }

            $tenantName  = $tenant->full_name;
            $propertyRef = $property->reference ?? 'N/A';
            $amount      = number_format((float) $monthly->remaining_amount, 0, ',', ' ');

            foreach ($levels as $level => $days) {
                if ($daysOverdue < $days) {
                    break; // Not yet overdue enough for this level
                }

                // Check if a reminder for this level already exists for this monthly
                $exists = Reminder::where('lease_monthly_id', $monthly->id)
                    ->where('level', $level)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $message = $this->buildMessage($level, $tenantName, $propertyRef, $monthly->month, $amount);
                $channel = $this->determineChannel($tenant);

                $this->createReminder($monthly, $channel, $message, $level);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Determine the best channel for sending to a tenant.
     * Priority: WhatsApp > SMS > Email
     */
    public function determineChannel(object $tenant): string
    {
        if (!empty($tenant->whatsapp_phone) && $this->twilioService->isWhatsAppConfigured()) {
            return 'whatsapp';
        }

        if (!empty($tenant->phone) && $this->twilioService->isSmsConfigured()) {
            return 'sms';
        }

        if (!empty($tenant->email)) {
            return 'email';
        }

        return 'sms'; // Fallback
    }

    /**
     * Build the reminder message based on level.
     */
    public function buildMessage(int $level, string $tenantName, string $propertyRef, string $month, string $amount): string
    {
        $signature = Setting::get('reminder_company_signature', 'MDA Patrimoine');

        $defaultMessages = [
            1 => "Cher(e) {tenantName}, nous vous informons que le loyer du mois de {month} pour le bien {propertyRef} reste impaye. Montant restant: {amount} FCFA. Merci de regulariser votre situation dans les plus brefs delais.",
            2 => "Cher(e) {tenantName}, malgre notre precedent rappel, le loyer du mois de {month} ({amount} FCFA) pour le bien {propertyRef} reste impaye. Merci de proceder au reglement sous 48 heures.",
            3 => "MISE EN DEMEURE — Cher(e) {tenantName}, le loyer du mois de {month} ({amount} FCFA) pour le bien {propertyRef} est en retard de plus de 15 jours. Sans regularisation sous 72 heures, des poursuites pourront etre engagees conformement a la legislation en vigueur.",
        ];

        $template = Setting::get("reminder_level{$level}_message", $defaultMessages[$level] ?? $defaultMessages[1]);

        $message = str_replace(
            ['{tenantName}', '{month}', '{propertyRef}', '{amount}'],
            [$tenantName, $month, $propertyRef, $amount],
            $template
        );

        return "{$message} — {$signature}";
    }

    /* ------------------------------------------------------------------ */
    /*  Private channel senders                                            */
    /* ------------------------------------------------------------------ */

    private function sendViaWhatsApp(Reminder $reminder, object $tenant): bool
    {
        $phone = $tenant->whatsapp_phone ?: $tenant->phone;

        if (empty($phone)) {
            return $this->fallbackToSms($reminder, $tenant);
        }

        $result = $this->twilioService->sendWhatsApp($phone, $reminder->message);

        if ($result['success']) {
            $reminder->update([
                'status'      => 'envoye',
                'sent_at'     => now(),
                'external_id' => $result['sid'],
            ]);
            return true;
        }

        // WhatsApp failed → fallback to SMS
        Log::warning("WhatsApp failed for reminder #{$reminder->id}, falling back to SMS", [
            'error' => $result['error'],
        ]);

        return $this->fallbackToSms($reminder, $tenant);
    }

    private function fallbackToSms(Reminder $reminder, object $tenant): bool
    {
        if (empty($tenant->phone)) {
            $reminder->update([
                'status'        => 'echec',
                'error_message' => 'Aucun numero de telephone disponible.',
                'sent_at'       => now(),
            ]);
            return false;
        }

        $result = $this->twilioService->sendSms($tenant->phone, $reminder->message);

        $reminder->update([
            'status'        => $result['success'] ? 'envoye' : 'echec',
            'sent_at'       => now(),
            'external_id'   => $result['sid'],
            'error_message' => $result['error'],
            'channel'       => 'sms', // Update channel to reflect actual delivery method
        ]);

        return $result['success'];
    }

    private function sendViaSms(Reminder $reminder, object $tenant): bool
    {
        if (empty($tenant->phone)) {
            $reminder->update([
                'status'        => 'echec',
                'error_message' => 'Aucun numero de telephone disponible.',
                'sent_at'       => now(),
            ]);
            return false;
        }

        $result = $this->twilioService->sendSms($tenant->phone, $reminder->message);

        $reminder->update([
            'status'        => $result['success'] ? 'envoye' : 'echec',
            'sent_at'       => now(),
            'external_id'   => $result['sid'],
            'error_message' => $result['error'],
        ]);

        return $result['success'];
    }

    private function sendViaEmail(Reminder $reminder, object $tenant): bool
    {
        if (empty($tenant->email)) {
            $reminder->update([
                'status'        => 'echec',
                'error_message' => 'Aucune adresse email disponible.',
                'sent_at'       => now(),
            ]);
            return false;
        }

        $monthly  = $reminder->leaseMonthly;
        $property = $monthly->lease->property ?? null;

        try {
            Mail::to($tenant->email)->send(new ReminderMail(
                reminder: $reminder,
                tenantName: $tenant->full_name,
                propertyRef: $property->reference ?? 'N/A',
                month: $monthly->month,
                remainingAmount: (float) $monthly->remaining_amount,
                level: $reminder->level,
            ));

            $reminder->update([
                'status'  => 'envoye',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Email send failed for reminder #{$reminder->id}", [
                'error' => $e->getMessage(),
            ]);

            $reminder->update([
                'status'        => 'echec',
                'error_message' => $e->getMessage(),
                'sent_at'       => now(),
            ]);

            return false;
        }
    }

    private function markAsSent(Reminder $reminder): bool
    {
        $reminder->update([
            'status'  => 'envoye',
            'sent_at' => now(),
        ]);

        return true;
    }
}
