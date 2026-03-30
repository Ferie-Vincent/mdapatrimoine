<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajout de SoftDeletes sur les tables financières critiques.
     * Protège contre la suppression accidentelle de données financières.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('lease_monthlies', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('deposit_refunds', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('lease_monthlies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('deposit_refunds', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
