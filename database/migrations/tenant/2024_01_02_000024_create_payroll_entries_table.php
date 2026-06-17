<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('fixed_allowance', 15, 2)->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->decimal('overtime_pay', 15, 2)->default(0);
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->boolean('is_skipped')->default(false);
            $table->string('skip_reason')->nullable();
            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id'], 'payroll_entry_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_entries');
    }
};
