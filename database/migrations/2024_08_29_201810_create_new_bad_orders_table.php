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
        Schema::create('new_bad_orders', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code');
            $table->unsignedBigInteger('customer_id');
            $table->string('degic_code');
            $table->string('bo_percentage');
            $table->string('remarks')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedInteger('inbound_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_bad_orders');
    }
};
