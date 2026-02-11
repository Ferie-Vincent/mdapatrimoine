<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->enum('type', ['appartement', 'maison', 'studio', 'bureau', 'commerce', 'terrain', 'autre'])->default('appartement');
            $table->text('address');
            $table->string('city')->nullable();
            $table->text('description')->nullable();
            $table->decimal('surface', 10, 2)->nullable();
            $table->integer('rooms')->nullable();
            $table->enum('status', ['disponible', 'occupe', 'travaux'])->default('disponible');
            $table->decimal('rent_reference', 12, 2)->default(0);
            $table->decimal('charges_reference', 12, 2)->default(0);
            $table->integer('nb_keys')->nullable();
            $table->integer('nb_clim')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sci_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
