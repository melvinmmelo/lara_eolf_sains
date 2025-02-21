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
        Schema::create('pricelevels', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code', 15);
            $table->string('pl_name');
            $table->string('pl_desc');
            $table->string('pl_status');
            $table->string('pl_type', 15);
            $table->timestamps();
            $table->index('branch_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelevels');
    }
};
