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
        Schema::create('inbounds', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('branch_code', 15);
            $table->string('equipment_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('store_id');
            $table->string('driver_id');
            $table->string('vehicle_id');
            $table->json('products')->nullable();
            $table->tinyInteger('with_invoice')->nullable();
            $table->tinyInteger('bad_order')->nullable();
            $table->string('status', 10);
            $table->unsignedInteger('pricelevel_id');
            $table->timestamps();

            $table->index('branch_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbounds');
    }
};
