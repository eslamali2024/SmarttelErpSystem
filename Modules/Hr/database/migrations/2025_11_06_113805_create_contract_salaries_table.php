<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contract_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('employee_contracts')->onDelete('cascade');
            $table->decimal('basic_salary', 15, 3)->nullable();
            $table->decimal('gross_salary', 15, 3)->nullable();
            $table->decimal('net_salary', 15, 3)->nullable();
            $table->decimal('salary_per_day', 15, 3)->nullable();
            $table->decimal('salary_per_hour', 15, 3)->nullable();
            $table->decimal('salary_per_year', 15, 3)->nullable();
            $table->decimal('total_allowances', 15, 3)->nullable();
            $table->decimal('social_insurance', 15, 3)->nullable();
            $table->decimal('insurance_wage', 15, 3)->nullable();
            $table->decimal('insurance_wage_rounded', 15, 3)->nullable();
            $table->decimal('company_insurance', 15, 3)->nullable();
            $table->decimal('martyrs_families_fund', 15, 3)->nullable();
            $table->decimal('tax_monthly', 15, 3)->nullable();
            $table->decimal('total_deductions', 15, 3)->nullable();

            $table->string('insurance_number')->nullable();
            $table->foreignId('insurance_company_id')->nullable()->constrained('insurance_companies')->noActionOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->noActionOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_salaries');
    }
};
