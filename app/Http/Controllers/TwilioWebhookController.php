<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TwilioWebhookController extends Controller
{
    /**
     * Handle Twilio status callback for message delivery tracking.
     *
     * Twilio sends status updates: queued, sent, delivered, undelivered, failed.
     */
    public function status(Request $request): Response
    {
        $messageSid  = $request->input('MessageSid');
        $status      = $request->input('MessageStatus');
        $errorCode   = $request->input('ErrorCode');
        $errorMessage = $request->input('ErrorMessage');

        if (empty($messageSid)) {
            return response('Missing MessageSid', 400);
        }

        $reminder = Reminder::where('external_id', $messageSid)->first();

        if (!$reminder) {
            Log::warning("Twilio webhook: no reminder found for SID {$messageSid}");
            return response('OK', 200);
        }

        match ($status) {
            'delivered', 'read' => $reminder->update([
                'delivered_at' => now(),
                'status'       => 'envoye',
            ]),
            'failed', 'undelivered' => $reminder->update([
                'status'        => 'echec',
                'error_message' => $errorMessage ?: "Twilio error code: {$errorCode}",
            ]),
            default => null,
        };

        Log::info("Twilio webhook: {$status} for SID {$messageSid}", [
            'reminder_id' => $reminder->id,
            'error_code'  => $errorCode,
        ]);

        return response('OK', 200);
    }
}
