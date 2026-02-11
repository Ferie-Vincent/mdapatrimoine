<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add receipt_path to service_provisions
        Schema::table('service_provisions', function (Blueprint $table) {
            $table->string('receipt_path')->nullable()->after('status');
        });

        // Add receipt_path to material_purchases
        Schema::table('material_purchases', function (Blueprint $table) {
            $table->string('receipt_path')->nullable()->after('amount');
        });

        // Monthly budgets (Caisse)
        Schema::create('monthly_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('year');
            $table->enum('type', ['prestations', 'achats']);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['sci_id', 'month', 'year', 'type']);
        });

        // Fixed charges
        Schema::create('fixed_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('year');
            $table->enum('charge_type', ['cie', 'sodeci', 'honoraire']);
            $table->string('label')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();
            $table->index(['sci_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_charges');
        Schema::dropIfExists('monthly_budgets');

        Schema::table('material_purchases', function (Blueprint $table) {
            $table->dropColumn('receipt_path');
        });

        Schema::table('service_provisions', function (Blueprint $table) {
            $table->dropColumn('receipt_path');
        });
    }
};
