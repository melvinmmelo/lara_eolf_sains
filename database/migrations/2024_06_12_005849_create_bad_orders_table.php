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
        Schema::create('bad_orders', function (Blueprint $table) {
            $table->id();
            $table->string('bo_id'); 
            // $table->unsignedBigInteger('inbound_id');
            $table->unsignedBigInteger('customer_id'); // Add this line to create the column
            $table->unsignedBigInteger('store_id');

            $table->string('re_dr');
            $table->string('bo_percentage')->nullable();
            $table->string('remarks')->nullable();

            $table->string('ptype_code');
            $table->string('code');
            $table->integer('quantity');
            $table->float('price');
            $table->string('unit');
            $table->string('description');
            $table->float('amount');
            
            // $table->foreign('inbound_id')->references('id')->on('inbounds')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bad_orders');
    }
};
