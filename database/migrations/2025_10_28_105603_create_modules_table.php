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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('key', 30)->unique();
            $table->string('parent_key', 30)->nullable();
            $table->string('name')->nullable();
            $table->string('path')->nullable()->default('#');
            $table->string('permission_title')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('status')->nullable()->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->foreign('parent_key')
                ->references('key')
                ->on('modules')
                ->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
