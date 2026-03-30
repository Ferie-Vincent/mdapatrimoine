<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE leases MODIFY `payment_method` ENUM('virement','especes','cheque','mobile_money','depot_bancaire','autre') NULL");
        DB::statement("ALTER TABLE payments MODIFY `method` ENUM('virement','especes','cheque','mobile_money','depot_bancaire','autre') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE leases MODIFY `payment_method` ENUM('virement','especes','cheque','mobile_money','autre') NULL");
        DB::statement("ALTER TABLE payments MODIFY `method` ENUM('virement','especes','cheque','mobile_money','autre') NULL");
    }
};
