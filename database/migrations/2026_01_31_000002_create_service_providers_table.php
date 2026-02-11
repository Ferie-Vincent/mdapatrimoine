<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('phone_secondary')->nullable();
            $table->string('email')->nullable();
            $table->enum('category', [
                'artisan', 'manoeuvre', 'plombier', 'electricien',
                'peintre', 'menuisier', 'macon', 'serrurier',
                'climatiseur', 'autre',
            ])->default('artisan');
            $table->string('custom_category')->nullable();
            $table->string('specialty')->nullable();
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
