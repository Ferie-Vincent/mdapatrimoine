<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('refunded_at');
            $table->string('method')->nullable();
            $table->string('reference')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_refunds');
    }
};
