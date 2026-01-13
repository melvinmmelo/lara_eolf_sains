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
        Schema::create('bad_order_prices', function (Blueprint $table) {
            $table->id();
            $table->string('ptype_code', 191);
            $table->string('ptype_name', 191);
            $table->unsignedBigInteger('price_level_id');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('ptype_code')->references('code')->on('product_types')->onDelete('cascade');
            $table->foreign('price_level_id')->references('id')->on('pricelevels')->onDelete('cascade');

            // Indexes for better query performance
            $table->index('ptype_code');
            $table->index('price_level_id');

            // Unique constraint to prevent duplicate entries
            $table->unique(['ptype_code', 'price_level_id'], 'bad_order_prices_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bad_order_prices');
    }
};
