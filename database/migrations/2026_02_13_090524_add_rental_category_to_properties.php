<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->enum('rental_category', ['non_meuble', 'meuble'])->default('non_meuble')->after('status');
            $table->decimal('daily_rate', 12, 2)->nullable()->after('rental_category');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['rental_category', 'daily_rate']);
        });
    }
};
