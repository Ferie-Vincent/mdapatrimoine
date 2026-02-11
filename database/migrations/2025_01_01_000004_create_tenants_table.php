<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('phone_secondary')->nullable();
            $table->text('address')->nullable();
            $table->string('id_type')->nullable(); // CNI, Passeport, etc.
            $table->string('id_number')->nullable();
            $table->date('id_expiration')->nullable();
            $table->string('id_file_path')->nullable();
            $table->string('profession')->nullable();
            $table->string('employer')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('guarantor_name')->nullable();
            $table->string('guarantor_phone')->nullable();
            $table->text('guarantor_address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('sci_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
