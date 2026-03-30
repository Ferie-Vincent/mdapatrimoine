<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('recurrence', ['quotidien', 'hebdomadaire', 'mensuel', 'trimestriel', 'annuel'])
                  ->nullable()
                  ->default(null)
                  ->after('is_auto');
            $table->date('recurrence_end_date')
                  ->nullable()
                  ->after('recurrence');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['recurrence', 'recurrence_end_date']);
        });
    }
};
