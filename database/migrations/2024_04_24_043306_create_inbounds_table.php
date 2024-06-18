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
            $table->unsignedInteger('user_id');
            $table->string('branch_code', 15);
            $table->unsignedInteger('equipment_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('driver_id');
            $table->unsignedInteger('vehicle_id');
            $table->tinyInteger('bad_order')->nullable();
            $table->unsignedInteger('bad_order_id')->nullable();
            $table->float('bo_amount', 2)->nullable();
            $table->json('products')->nullable();
            $table->tinyInteger('with_invoice')->nullable();
            $table->tinyInteger('bad_order')->nullable();
            $table->string('status', 10);
            $table->unsignedInteger('pricelevel_id');
            $table->string('payment_type', 30)->nullable();
            $table->string('ref_no', 30)->nullable();
            $table->float('delivered_amount', 2)->nullable();
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
