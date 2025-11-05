<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Hr\Enums\ContractStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('cascade');
            $table->foreignId('time_management_id')->nullable()->constrained('time_managements')->onDelete('cascade');
            $table->foreignId('work_schedule_id')->nullable()->constrained('work_schedules')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes', 2000)->nullable();
            $table->integer('status')->default(ContractStatusEnum::ACTIVE)->nullable()->comment('1 = Active, 2 = Inactive in enum ContractStatusEnum.php');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_contracts');
    }
};
