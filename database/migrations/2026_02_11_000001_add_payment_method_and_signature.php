<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_provisions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->text('signature_data')->nullable()->after('receipt_path');
        });

        Schema::table('material_purchases', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('amount');
            $table->text('signature_data')->nullable()->after('receipt_path');
        });

        Schema::table('fixed_charges', function (Blueprint $table) {
            $table->text('signature_data')->nullable()->after('receipt_path');
        });
    }

    public function down(): void
    {
        Schema::table('service_provisions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'signature_data']);
        });

        Schema::table('material_purchases', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'signature_data']);
        });

        Schema::table('fixed_charges', function (Blueprint $table) {
            $table->dropColumn('signature_data');
        });
    }
};
