<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index composites pour optimiser les requêtes fréquentes.
     * Les FK simples (via constrained()) créent déjà des index mono-colonne.
     * On ajoute ici les index composites qui correspondent aux patterns de requêtes.
     */
    public function up(): void
    {
        // Payments : filtrage par SCI + plage de dates (exports, dashboard, rapports)
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['sci_id', 'paid_at'], 'idx_payments_sci_paid_at');
        });

        // Leases : recherche des baux actifs d'un locataire
        Schema::table('leases', function (Blueprint $table) {
            $table->index(['tenant_id', 'status'], 'idx_leases_tenant_status');
            $table->index(['lease_type', 'status'], 'idx_leases_type_status');
        });

        // Reminders : relances en attente par SCI
        Schema::table('reminders', function (Blueprint $table) {
            $table->index(['sci_id', 'status'], 'idx_reminders_sci_status');
        });

        // Service Providers : liste des prestataires actifs par SCI
        Schema::table('service_providers', function (Blueprint $table) {
            $table->index(['sci_id', 'is_active'], 'idx_service_providers_sci_active');
        });

        // Provider Contracts : contrats par SCI filtrés par statut
        Schema::table('provider_contracts', function (Blueprint $table) {
            $table->index(['sci_id', 'status'], 'idx_provider_contracts_sci_status');
        });

        // Deposit Refunds : recherche par bail dans une SCI
        Schema::table('deposit_refunds', function (Blueprint $table) {
            $table->index(['sci_id', 'lease_id'], 'idx_deposit_refunds_sci_lease');
        });

        // Documents : tri chronologique (timeline, listing récent)
        Schema::table('documents', function (Blueprint $table) {
            $table->index('created_at', 'idx_documents_created_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_sci_paid_at');
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex('idx_leases_tenant_status');
            $table->dropIndex('idx_leases_type_status');
        });

        Schema::table('reminders', function (Blueprint $table) {
            $table->dropIndex('idx_reminders_sci_status');
        });

        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropIndex('idx_service_providers_sci_active');
        });

        Schema::table('provider_contracts', function (Blueprint $table) {
            $table->dropIndex('idx_provider_contracts_sci_status');
        });

        Schema::table('deposit_refunds', function (Blueprint $table) {
            $table->dropIndex('idx_deposit_refunds_sci_lease');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('idx_documents_created_at');
        });
    }
};
