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
        Schema::create('work_schedule_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_schedule_id')->nullable()->constrained('work_schedules')->onDelete('cascade');
            $table->integer('deducation_after_time')->nullable();
            $table->double('deducation_percentage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_rules');
    }
};
