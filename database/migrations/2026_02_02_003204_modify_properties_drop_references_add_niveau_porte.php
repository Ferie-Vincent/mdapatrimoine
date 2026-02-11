<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['rent_reference', 'charges_reference']);
            $table->string('niveau')->nullable()->after('rooms');
            $table->string('numero_porte')->nullable()->after('niveau');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('rent_reference', 12, 2)->default(0);
            $table->decimal('charges_reference', 12, 2)->default(0);
            $table->dropColumn(['niveau', 'numero_porte']);
        });
    }
};
