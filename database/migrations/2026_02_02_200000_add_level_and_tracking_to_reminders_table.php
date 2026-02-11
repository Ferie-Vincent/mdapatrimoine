<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')->default(1)->after('status');
            $table->text('error_message')->nullable()->after('sent_by');
            $table->string('external_id')->nullable()->after('error_message');
            $table->timestamp('delivered_at')->nullable()->after('external_id');
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn(['level', 'error_message', 'external_id', 'delivered_at']);
        });
    }
};
