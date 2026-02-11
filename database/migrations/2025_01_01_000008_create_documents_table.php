<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->string('type'); // quittance, recu, attestation_location, avis_echeance, relance, releve_compte, recap_mensuel, attestation_reception_fonds, attestation_bail, attestation_sortie
            $table->string('related_type')->nullable(); // App\Models\Lease, Payment, etc.
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('month', 7)->nullable(); // YYYY-MM
            $table->string('path');
            $table->json('meta')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sci_id', 'type']);
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
