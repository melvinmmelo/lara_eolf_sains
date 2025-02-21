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
        Schema::create('new_inbound_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inbound_id')->default(0);
            $table->string('branch_code');
            $table->string('order');
            $table->string('ptype_code');
            $table->string('code');
            $table->string('description');
            $table->integer('old_quantity');
            $table->integer('quantity');
            $table->float('price', 8, 2);
            $table->string('unit');
            $table->string('status')->nullable();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_inbound_products');
    }
};
