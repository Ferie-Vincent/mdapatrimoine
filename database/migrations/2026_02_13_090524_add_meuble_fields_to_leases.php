<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->enum('lease_type', ['classique', 'meuble'])->default('classique')->after('status');
            $table->date('check_in_date')->nullable()->after('lease_type');
            $table->date('check_out_date')->nullable()->after('check_in_date');
            $table->unsignedInteger('nights_count')->nullable()->after('check_out_date');
            $table->decimal('daily_rate', 12, 2)->nullable()->after('nights_count');
            $table->decimal('discount_rate', 5, 2)->default(0)->after('daily_rate');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_rate');
            $table->decimal('total_amount', 12, 2)->nullable()->after('discount_amount');
        });

        // Make due_day nullable for meuble leases
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE leases MODIFY due_day TINYINT UNSIGNED NULL DEFAULT 5');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE leases MODIFY due_day TINYINT UNSIGNED NOT NULL DEFAULT 5');
        }

        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn([
                'lease_type', 'check_in_date', 'check_out_date', 'nights_count',
                'daily_rate', 'discount_rate', 'discount_amount', 'total_amount',
            ]);
        });
    }
};
