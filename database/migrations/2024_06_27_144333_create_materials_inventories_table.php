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
        Schema::create('materials_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code');
            $table->string('name');
            $table->string('unit')->nullable();
            $table->integer('quantity')->nullable();
            $table->float('amount', 2)->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedInteger('withdrawal_id')->nullable();
            $table->timestamps();
            $table->string('modified_by', 99);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials_inventories');
    }
};
