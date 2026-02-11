<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::allAsArray();

        // Mask the Twilio auth token for display
        $maskedToken = '';
        $token = $settings['twilio_auth_token'] ?? '';
        if (strlen($token) > 4) {
            $maskedToken = str_repeat('*', strlen($token) - 4) . substr($token, -4);
        } elseif ($token !== '') {
            $maskedToken = $token;
        }

        return view('settings.index', compact('settings', 'maskedToken'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // Relances
            'reminder_level1_days'    => ['required', 'integer', 'min:1', 'max:90'],
            'reminder_level2_days'    => ['required', 'integer', 'min:1', 'max:90'],
            'reminder_level3_days'    => ['required', 'integer', 'min:1', 'max:90'],
            'reminder_level1_message' => ['required', 'string', 'max:1000'],
            'reminder_level2_message' => ['required', 'string', 'max:1000'],
            'reminder_level3_message' => ['required', 'string', 'max:1000'],
            'reminder_company_signature' => ['required', 'string', 'max:100'],

            // Twilio
            'twilio_sid'            => ['nullable', 'string', 'max:255'],
            'twilio_auth_token'     => ['nullable', 'string', 'max:255'],
            'twilio_whatsapp_from'  => ['nullable', 'string', 'max:50'],
            'twilio_sms_from'       => ['nullable', 'string', 'max:50'],

            // Valeurs par défaut baux
            'default_penalty_rate'      => ['required', 'numeric', 'min:0', 'max:100'],
            'default_penalty_delay_days' => ['required', 'integer', 'min:0', 'max:90'],
            'default_due_day'           => ['required', 'integer', 'min:1', 'max:28'],
        ]);

        // If the token field is empty or unchanged (masked), keep the existing value
        $currentToken = Setting::get('twilio_auth_token', '');
        if (empty($data['twilio_auth_token']) || $data['twilio_auth_token'] === str_repeat('*', max(0, strlen($currentToken) - 4)) . substr($currentToken, -4)) {
            $data['twilio_auth_token'] = $currentToken;
        }

        foreach ($data as $key => $value) {
            Setting::set($key, (string) ($value ?? ''));
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Parametres mis a jour avec succes.');
    }
}
