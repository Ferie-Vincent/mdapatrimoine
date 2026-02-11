<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioService
{
    private ?Client $client = null;

    private function getSetting(string $settingKey, string $configKey): string
    {
        return Setting::get($settingKey) ?: (string) config($configKey, '');
    }

    private function client(): Client
    {
        if ($this->client === null) {
            $sid   = $this->getSetting('twilio_sid', 'services.twilio.sid');
            $token = $this->getSetting('twilio_auth_token', 'services.twilio.token');

            if (empty($sid) || empty($token)) {
                throw new \RuntimeException('Twilio credentials are not configured.');
            }

            $this->client = new Client($sid, $token);
        }

        return $this->client;
    }

    /**
     * Send a WhatsApp message via Twilio.
     *
     * @return array{success: bool, sid: string|null, error: string|null}
     */
    public function sendWhatsApp(string $to, string $message): array
    {
        $from = $this->getSetting('twilio_whatsapp_from', 'services.twilio.whatsapp_from');

        if (empty($from)) {
            return ['success' => false, 'sid' => null, 'error' => 'TWILIO_WHATSAPP_FROM not configured.'];
        }

        // Ensure the "to" number has the whatsapp: prefix
        $to = str_starts_with($to, 'whatsapp:') ? $to : "whatsapp:{$to}";

        return $this->send($from, $to, $message);
    }

    /**
     * Send an SMS via Twilio.
     *
     * @return array{success: bool, sid: string|null, error: string|null}
     */
    public function sendSms(string $to, string $message): array
    {
        $from = $this->getSetting('twilio_sms_from', 'services.twilio.sms_from');

        if (empty($from)) {
            return ['success' => false, 'sid' => null, 'error' => 'TWILIO_SMS_FROM not configured.'];
        }

        return $this->send($from, $to, $message);
    }

    /**
     * Internal send method.
     *
     * @return array{success: bool, sid: string|null, error: string|null}
     */
    private function send(string $from, string $to, string $message): array
    {
        try {
            $twilioMessage = $this->client()->messages->create($to, [
                'from' => $from,
                'body' => $message,
                'statusCallback' => url('/webhooks/twilio/status'),
            ]);

            Log::info("Twilio message sent", [
                'sid'     => $twilioMessage->sid,
                'to'      => $to,
                'channel' => str_starts_with($from, 'whatsapp:') ? 'whatsapp' : 'sms',
            ]);

            return [
                'success' => true,
                'sid'     => $twilioMessage->sid,
                'error'   => null,
            ];
        } catch (\Twilio\Exceptions\RestException $e) {
            Log::error("Twilio send failed", [
                'to'      => $to,
                'error'   => $e->getMessage(),
                'code'    => $e->getCode(),
            ]);

            return [
                'success' => false,
                'sid'     => null,
                'error'   => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            Log::error("Twilio unexpected error", [
                'to'    => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'sid'     => null,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if Twilio is properly configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->getSetting('twilio_sid', 'services.twilio.sid'))
            && !empty($this->getSetting('twilio_auth_token', 'services.twilio.token'));
    }

    /**
     * Check if WhatsApp sending is configured.
     */
    public function isWhatsAppConfigured(): bool
    {
        return $this->isConfigured() && !empty($this->getSetting('twilio_whatsapp_from', 'services.twilio.whatsapp_from'));
    }

    /**
     * Check if SMS sending is configured.
     */
    public function isSmsConfigured(): bool
    {
        return $this->isConfigured() && !empty($this->getSetting('twilio_sms_from', 'services.twilio.sms_from'));
    }
}
