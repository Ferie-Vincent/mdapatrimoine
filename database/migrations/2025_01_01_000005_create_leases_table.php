<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration_months')->nullable();
            $table->decimal('rent_amount', 12, 2);
            $table->decimal('charges_amount', 12, 2)->default(0);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['virement', 'especes', 'cheque', 'mobile_money', 'autre'])->default('especes');
            $table->unsignedTinyInteger('due_day')->default(5); // jour du mois pour échéance
            $table->decimal('penalty_rate', 5, 2)->default(0); // % pénalité retard
            $table->integer('penalty_delay_days')->default(0); // jours de grâce avant pénalité
            $table->enum('status', ['actif', 'resilie', 'en_attente', 'expire'])->default('en_attente');
            $table->date('termination_date')->nullable();
            $table->text('termination_reason')->nullable();
            $table->string('signed_lease_path')->nullable();
            $table->string('entry_inspection_path')->nullable();
            $table->string('exit_inspection_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sci_id', 'status']);
            $table->index(['property_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
