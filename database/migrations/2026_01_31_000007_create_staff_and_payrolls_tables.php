<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('role')->nullable();          // poste / fonction
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->decimal('base_salary', 12, 2)->nullable();
            $table->date('hire_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['sci_id', 'is_active']);
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_member_id')->constrained('staff_members')->cascadeOnDelete();
            $table->foreignId('sci_id')->constrained('scis')->cascadeOnDelete();
            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 12, 2);
            $table->date('paid_at')->nullable();
            $table->string('payment_method')->nullable();  // especes, virement, cheque, mobile_money
            $table->string('reference')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['staff_member_id', 'month', 'year']);
            $table->index(['sci_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('staff_members');
    }
};
