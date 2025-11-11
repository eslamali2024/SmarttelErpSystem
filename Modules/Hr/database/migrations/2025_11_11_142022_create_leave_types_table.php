<?php

use Illuminate\Support\Facades\Schema;
use Modules\Hr\Enums\LeaveTypeUnitEnum;
use Illuminate\Database\Schema\Blueprint;
use Modules\Hr\Enums\LeaveTypeDurationEnum;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('unit')->nullable()->default(LeaveTypeUnitEnum::DAYS);
            $table->integer('type_duration')->nullable()->default(LeaveTypeDurationEnum::FIXED);
            $table->boolean('for_employee')->nullable()->default(false);
            $table->decimal('duration_deducted_percentage', 10, 3)->nullable();
            $table->decimal('salary_deducted_percentage', 10, 3)->nullable();
            $table->decimal('company_amount', 10, 3)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
