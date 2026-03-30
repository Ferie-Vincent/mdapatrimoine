<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite already stores strings as TEXT, no need to modify column type
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE leases MODIFY entry_inspection_path TEXT NULL');
        }

        $leases = DB::table('leases')->whereNotNull('entry_inspection_path')->get();
        foreach ($leases as $lease) {
            if (str_starts_with($lease->entry_inspection_path, '[')) {
                continue;
            }
            DB::table('leases')->where('id', $lease->id)->update([
                'entry_inspection_path' => json_encode([$lease->entry_inspection_path]),
            ]);
        }
    }

    public function down(): void
    {
        $leases = DB::table('leases')->whereNotNull('entry_inspection_path')->get();
        foreach ($leases as $lease) {
            $paths = json_decode($lease->entry_inspection_path, true);
            DB::table('leases')->where('id', $lease->id)->update([
                'entry_inspection_path' => is_array($paths) ? ($paths[0] ?? null) : $lease->entry_inspection_path,
            ]);
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE leases MODIFY entry_inspection_path VARCHAR(255) NULL');
        }
    }
};
