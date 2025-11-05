<?php

use Illuminate\Support\Facades\Schema;
use Modules\Hr\Enums\ContractStatusEnum;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contract_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('employee_contracts')->onDelete('cascade');
            $table->foreignId('division_id')->nullable()->constrained('divisions')->noActionOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->noActionOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->noActionOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->noActionOnDelete();
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
        Schema::dropIfExists('contract_positions');
    }
};
