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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->noActionOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->noActionOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->noActionOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->noActionOnDelete();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
