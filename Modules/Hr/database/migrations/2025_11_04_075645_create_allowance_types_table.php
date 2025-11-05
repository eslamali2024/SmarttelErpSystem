<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Hr\Enums\AllowancesTaxableEnum;
use Modules\Hr\Enums\AllowancesTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('allowance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('type')->default(AllowancesTypeEnum::RECURRING)->nullable();
            $table->integer('taxable')->default(AllowancesTaxableEnum::NOT_TAXABLE)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_types');
    }
};
