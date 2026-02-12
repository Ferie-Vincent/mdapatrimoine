<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'a_venir' to the enum
        DB::statement("ALTER TABLE lease_monthlies MODIFY COLUMN status ENUM('paye', 'partiel', 'impaye', 'en_retard', 'a_venir') DEFAULT 'impaye'");

        // Fix existing data: future unpaid monthlies should be 'a_venir'
        DB::table('lease_monthlies')
            ->where('status', 'impaye')
            ->where('due_date', '>', now())
            ->where('paid_amount', 0)
            ->update(['status' => 'a_venir']);
    }

    public function down(): void
    {
        // Revert 'a_venir' back to 'impaye'
        DB::table('lease_monthlies')
            ->where('status', 'a_venir')
            ->update(['status' => 'impaye']);

        DB::statement("ALTER TABLE lease_monthlies MODIFY COLUMN status ENUM('paye', 'partiel', 'impaye', 'en_retard') DEFAULT 'impaye'");
    }
};
