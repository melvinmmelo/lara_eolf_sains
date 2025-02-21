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
        Schema::create('item_master_data', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code');
            $table->string('product_code');
            $table->string('product_description');
            $table->string('unit');
            $table->unsignedInteger('stocks');
            $table->unsignedInteger('reserved')->default(0);
            $table->unsignedInteger('hold_quantity')->default(0);
            $table->json('hold_details')->nullable(); // save the DR ID, PCODE, AND QUANTITY, DATE;
            $table->timestamps();

            $table->unique(['branch_code', 'product_code']);
            $table->index('branch_code');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_master_data');
    }
};
