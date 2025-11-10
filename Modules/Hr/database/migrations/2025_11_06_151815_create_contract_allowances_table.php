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
        Schema::create('contract_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('employee_contracts')->onDelete('cascade');
            $table->foreignId('allowance_id')->constrained('allowance_types')->onDelete('cascade');
            $table->decimal('amount', 15, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_allowances');
    }
};
