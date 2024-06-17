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
            $table->string('branch_code', 15);
            $table->string('ownership');
            $table->string('type');
            $table->string('brand');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('serial_no')->unique();
            $table->string('model', 30)->nullable();
            $table->string('code');
            $table->string('distributor')->nullable();
            $table->date('date_delivered')->nullable();
            $table->date('date_purchased')->nullable();
            $table->string('status')->default('Active');
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
