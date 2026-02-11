<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_monthly_id')->constrained('lease_monthlies')->cascadeOnDelete();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('paid_at');
            $table->enum('method', ['virement', 'especes', 'cheque', 'mobile_money', 'autre'])->default('especes');
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('sci_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
