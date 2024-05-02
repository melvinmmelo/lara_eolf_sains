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
        Schema::create('equipment_store', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); // Add this line to create the column
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('equipment_id');
            $table->string('type');
            $table->string('brand');
            $table->string('serial');
            $table->string('owned');
            $table->string('pull_status');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('store_id')->references('id')->on('storeinfo')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); // Then define the foreign key constraint

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_store');
    }
};

