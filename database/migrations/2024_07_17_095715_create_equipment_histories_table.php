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
        Schema::create('equipment_histories', function (Blueprint $table) {
            $table->id();
            $table->string('serial_no');
            $table->string('degic_no');
            $table->tinyInteger('customer_id');
            $table->string('customer_name');
            $table->dateTime('date_assigned');
            $table->string('user_name_assigned');
            $table->dateTime('date_pulled_out')->nullable();
            $table->string('pull_out_reason')->nullable();
            $table->string('user_name_pulled_out')->nullable();
            $table->timestamps();
            $table->string('current_user_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_histories');
    }
};
