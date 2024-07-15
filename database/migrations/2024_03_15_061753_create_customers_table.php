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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('distributor');
            $table->string('branch_code', 15);
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename');
            $table->string('companyname');
            $table->string('tin')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('brgy')->nullable();
            $table->string('subdivision')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('branch_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
