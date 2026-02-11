<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lease_monthlies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->string('month', 7); // YYYY-MM
            $table->decimal('rent_due', 12, 2);
            $table->decimal('charges_due', 12, 2)->default(0);
            $table->decimal('penalty_due', 12, 2)->default(0);
            $table->decimal('total_due', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2);
            $table->enum('status', ['paye', 'partiel', 'impaye', 'en_retard'])->default('impaye');
            $table->date('due_date');
            $table->timestamps();

            $table->unique(['lease_id', 'month']);
            $table->index(['sci_id', 'month']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lease_monthlies');
    }
};
