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
        Schema::create('time_managements', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('payroll')->default(true)->nullable();
            $table->boolean('fingerprint_in')->default(true)->nullable();
            $table->boolean('fingerprint_out')->default(true)->nullable();
            $table->boolean('factors')->default(true)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_managements');
    }
};
