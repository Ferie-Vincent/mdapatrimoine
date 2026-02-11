<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default values
        $defaults = [
            'reminder_level1_days' => '3',
            'reminder_level2_days' => '7',
            'reminder_level3_days' => '15',
            'reminder_level1_message' => "Cher(e) {tenantName}, nous vous informons que le loyer du mois de {month} pour le bien {propertyRef} reste impaye. Montant restant: {amount} FCFA. Merci de regulariser votre situation dans les plus brefs delais.",
            'reminder_level2_message' => "Cher(e) {tenantName}, malgre notre precedent rappel, le loyer du mois de {month} ({amount} FCFA) pour le bien {propertyRef} reste impaye. Merci de proceder au reglement sous 48 heures.",
            'reminder_level3_message' => "MISE EN DEMEURE — Cher(e) {tenantName}, le loyer du mois de {month} ({amount} FCFA) pour le bien {propertyRef} est en retard de plus de 15 jours. Sans regularisation sous 72 heures, des poursuites pourront etre engagees conformement a la legislation en vigueur.",
            'reminder_company_signature' => 'MDA Patrimoine',
            'twilio_sid' => '',
            'twilio_auth_token' => '',
            'twilio_whatsapp_from' => '',
            'twilio_sms_from' => '',
            'default_penalty_rate' => '0',
            'default_penalty_delay_days' => '0',
            'default_due_day' => '5',
        ];

        $now = now();

        foreach ($defaults as $key => $value) {
            DB::table('settings')->insert([
                'key'        => $key,
                'value'      => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
