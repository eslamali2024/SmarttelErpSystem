<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Enums\Approval\ApprovalStatusEnum;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('deduction_type_id')->nullable()->constrained('deduction_types')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->decimal('amount', 10, 3)->nullable();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->integer('status')->default(ApprovalStatusEnum::PENDING)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
