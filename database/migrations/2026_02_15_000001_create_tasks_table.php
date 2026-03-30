<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('category', ['encaissement', 'relance', 'visite', 'administratif', 'document', 'autre']);
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->enum('priority', ['haute', 'moyenne', 'basse'])->default('moyenne');
            $table->enum('status', ['a_faire', 'en_cours', 'bloque', 'fait', 'archive'])->default('a_faire');
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->dateTime('reminder_at')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->boolean('is_auto')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            // Requete principale kanban
            $table->index(['user_id', 'status', 'scheduled_date']);
            // Filtre par SCI
            $table->index(['sci_id', 'scheduled_date']);
            // Deduplication auto-tasks
            $table->index(['related_type', 'related_id', 'scheduled_date']);
            // Job rappels push
            $table->index(['reminder_at', 'reminder_sent']);
            // Archivage
            $table->index(['status', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
