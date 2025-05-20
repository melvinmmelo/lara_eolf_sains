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
        Schema::create('inventory_bad_orders', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code')->default(session('branch_code'));
            $table->string('reference_name')->unique();
            $table->json('products');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('editing');
            $table->string('remarks')->nullable();
            $table->date('date_created')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_bad_orders');
    }
};
