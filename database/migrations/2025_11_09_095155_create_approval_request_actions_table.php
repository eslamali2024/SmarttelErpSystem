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
        Schema::create('approval_request_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained('approval_requests')->onDelete('cascade');
            $table->foreignId('approval_flow_id')->constrained('approval_flows')->onDelete('cascade');
            $table->foreignId('approval_flow_step_id')->constrained('approval_flow_steps')->onDelete('cascade');
            $table->integer('action_type')->default(0);
            $table->string('approver_name')->nullable();
            $table->foreignId('approver_id')->constrained('users')->noActionOnDelete()->nullable();
            $table->text('comment')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_request_actions');
    }
};
