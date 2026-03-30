<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE fixed_charges MODIFY charge_type ENUM('cie', 'sodeci', 'honoraire', 'autre') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE fixed_charges MODIFY charge_type ENUM('cie', 'sodeci', 'honoraire') NOT NULL");
        }
    }
};
