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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('pricelevel_id');
            $table->string('p_code');
            $table->string('p_unit');
            $table->string('p_quant');
            $table->float('p_price',2);
            $table->timestamps();

            // create a unique index for the combination of pricelevel_id and p_code
            $table->unique(['pricelevel_id', 'p_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
