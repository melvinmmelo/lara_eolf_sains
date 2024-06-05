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
        Schema::create('product_types', function (Blueprint $table) {
            $table->string('code')->unique()->primary();
            $table->string('name');
            $table->string('volume');
            $table->integer('spoon_pcs_per_bag')->default(0);
            $table->string('bo_pricing')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->integer('sequence_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_types');
    }
};
