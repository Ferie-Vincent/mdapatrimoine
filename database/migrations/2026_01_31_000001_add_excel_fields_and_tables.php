<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Properties: compteurs + étage + type appartement ──
        Schema::table('properties', function (Blueprint $table) {
            $table->string('apartment_type_label')->nullable()->after('type');
            $table->string('floor_label')->nullable()->after('apartment_type_label');
            $table->string('cie_meter_number')->nullable()->after('nb_clim');
            $table->string('sodeci_meter_number')->nullable()->after('cie_meter_number');
        });

        // ── Tenants: champs garant supplémentaires ──
        Schema::table('tenants', function (Blueprint $table) {
            // guarantor_name, guarantor_phone, guarantor_address existent déjà
            $table->string('guarantor_id_number')->nullable()->after('guarantor_address');
            $table->string('guarantor_profession')->nullable()->after('guarantor_id_number');
        });

        // ── Leases: champs Excel ──
        Schema::table('leases', function (Blueprint $table) {
            $table->string('dossier_number')->nullable()->after('tenant_id');
            $table->string('agency_name')->nullable()->after('dossier_number');
            $table->date('entry_inventory_date')->nullable()->after('agency_name');
            $table->decimal('caution_2_mois', 12, 2)->nullable()->after('deposit_amount');
            $table->decimal('loyers_avances_2_mois', 12, 2)->nullable()->after('caution_2_mois');
            $table->decimal('frais_agence', 12, 2)->nullable()->after('loyers_avances_2_mois');
            $table->date('notice_deposit_date')->nullable()->after('exit_inspection_path');
            $table->date('exit_inventory_date')->nullable()->after('notice_deposit_date');
            $table->decimal('charges_due_amount', 12, 2)->nullable()->after('exit_inventory_date');
            $table->decimal('deposit_returned_amount', 12, 2)->nullable()->after('charges_due_amount');
            $table->text('debts_or_credits_note')->nullable()->after('deposit_returned_amount');
            $table->date('actual_exit_date')->nullable()->after('debts_or_credits_note');

            $table->unique(['sci_id', 'dossier_number']);
        });

        // ── Service Provisions (Prestations) ──
        Schema::create('service_provisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('year');
            $table->enum('service_type', [
                'ELECTRICITE', 'PLOMBERIE', 'MENUISERIE', 'SERRURIE',
                'VITRIER', 'CARRELAGE', 'PEINTURE', 'FERRONIER',
            ]);
            $table->string('agent');
            $table->date('service_date')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->index(['sci_id', 'year', 'month']);
        });

        // ── Material Purchases (Achats matériel) ──
        Schema::create('material_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('materials');
            $table->string('supplier');
            $table->date('purchase_date')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->timestamps();

            $table->index(['sci_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_purchases');
        Schema::dropIfExists('service_provisions');

        Schema::table('leases', function (Blueprint $table) {
            $table->dropUnique(['sci_id', 'dossier_number']);
            $table->dropColumn([
                'dossier_number', 'agency_name', 'entry_inventory_date',
                'caution_2_mois', 'loyers_avances_2_mois', 'frais_agence',
                'notice_deposit_date', 'exit_inventory_date', 'charges_due_amount',
                'deposit_returned_amount', 'debts_or_credits_note', 'actual_exit_date',
            ]);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['guarantor_id_number', 'guarantor_profession']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['apartment_type_label', 'floor_label', 'cie_meter_number', 'sodeci_meter_number']);
        });
    }
};
