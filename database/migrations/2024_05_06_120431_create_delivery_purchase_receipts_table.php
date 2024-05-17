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
        Schema::create('delivery_purchase_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code');
            $table->string('dr_no')->unique();
            $table->date('issue_date');
            $table->string('status')->default('Encoding');
            $table->json('products')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_purchase_receipts');
    }
};
