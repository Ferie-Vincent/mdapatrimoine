<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rccm')->nullable();
            $table->string('ifu')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_iban')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table for user-SCI assignment
        Schema::create('sci_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['sci_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sci_user');
        Schema::dropIfExists('scis');
    }
};
