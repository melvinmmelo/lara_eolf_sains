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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('ownership'); // Ownership type of the equipment
            $table->string('type'); // Type of equipment
            $table->string('brand'); // Brand of the equipment
            $table->decimal('price', 10, 2); // Price of the equipment
            $table->string('serial_no')->unique(); // Serial number of the equipment
            $table->string('code')->nullable(); // Optional code for the equipment
            $table->string('distributor')->nullable(); // Distributor of the equipment
            $table->date('date_delivered')->nullable(); // Date the equipment was delivered
            $table->date('date_purchased')->nullable(); // Date the equipment was purchased
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

