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

        if (Schema::hasTable('products')) {
            Schema::drop('products');
        }

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->uniqiue();
            $table->string('product_type_code', 191);
            $table->string('product_variant_code', 191);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->unique(['product_type_code', 'product_variant_code'], 'unique_product_type_variant');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
