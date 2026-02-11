<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->foreignId('lease_monthly_id')->constrained('lease_monthlies')->cascadeOnDelete();
            $table->enum('channel', ['email', 'sms', 'whatsapp', 'courrier'])->default('email');
            $table->text('message');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['brouillon', 'envoye', 'echec'])->default('brouillon');
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('sci_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
