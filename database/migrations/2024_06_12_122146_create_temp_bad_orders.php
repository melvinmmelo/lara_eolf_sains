<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('temp_bad_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('store_id');
            $table->string('ptype_code');
            $table->string('code');
            $table->string('unit');
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('price', 8, 2);
            $table->decimal('amount', 8, 2);
            $table->string('session_id')->nullable();
            $table->timestamps();

            // You can add foreign key constraints if necessary
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            // $table->foreign('inbound_id')->references('id')->on('inbounds')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_bad_orders');
    }
};
