<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE monthly_budgets MODIFY `type` VARCHAR(20) NOT NULL DEFAULT 'global'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE monthly_budgets MODIFY `type` ENUM('prestations', 'achats') NOT NULL");
    }
};
