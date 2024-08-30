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
        Schema::create('new_temp_bad_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('new_bad_order_id')->nullable();
            $table->string('session_bo_id');
            $table->string('ptype_code');
            $table->string('description');
            $table->integer('quantity');
            $table->float('price');
            $table->timestamps();

            $table->index('new_bad_order_id');
            $table->unique(['session_bo_id', 'ptype_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_temp_bad_orders');
    }
};
