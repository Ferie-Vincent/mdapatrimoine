<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate old rows: merge prestations+achats into a single 'global' row per sci/month/year
        $groups = DB::table('monthly_budgets')
            ->whereIn('type', ['prestations', 'achats'])
            ->select('sci_id', 'month', 'year')
            ->groupBy('sci_id', 'month', 'year')
            ->get();

        foreach ($groups as $g) {
            $total = (float) DB::table('monthly_budgets')
                ->where('sci_id', $g->sci_id)
                ->where('month', $g->month)
                ->where('year', $g->year)
                ->whereIn('type', ['prestations', 'achats'])
                ->sum('amount');

            DB::table('monthly_budgets')
                ->where('sci_id', $g->sci_id)
                ->where('month', $g->month)
                ->where('year', $g->year)
                ->whereIn('type', ['prestations', 'achats'])
                ->delete();

            DB::table('monthly_budgets')->insert([
                'sci_id' => $g->sci_id,
                'month' => $g->month,
                'year' => $g->year,
                'type' => 'global',
                'amount' => $total,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop FK, drop old unique, add new unique without type, re-add FK
        Schema::table('monthly_budgets', function (Blueprint $table) {
            $table->dropForeign(['sci_id']);
            $table->dropUnique(['sci_id', 'month', 'year', 'type']);
        });

        Schema::table('monthly_budgets', function (Blueprint $table) {
            $table->unique(['sci_id', 'month', 'year']);
            $table->foreign('sci_id')->references('id')->on('scis')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('monthly_budgets', function (Blueprint $table) {
            $table->dropForeign(['sci_id']);
            $table->dropUnique(['sci_id', 'month', 'year']);
        });

        Schema::table('monthly_budgets', function (Blueprint $table) {
            $table->unique(['sci_id', 'month', 'year', 'type']);
            $table->foreign('sci_id')->references('id')->on('scis')->cascadeOnDelete();
        });
    }
};
