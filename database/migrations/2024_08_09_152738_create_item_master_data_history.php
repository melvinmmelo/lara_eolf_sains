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
        Schema::create('item_master_data_history', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code');
            $table->string('product_code');
            $table->string('product_description');
            $table->string('unit');
            $table->integer('stocks');
            $table->integer('reserved')->default(0);
            $table->integer('hold_quantity')->default(0);
            $table->json('hold_details')->nullable(); // save the DR ID, PCODE, AND QUANTITY, DATE;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_master_data_history');
    }
};
