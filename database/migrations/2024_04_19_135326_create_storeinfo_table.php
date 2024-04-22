<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreinfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storeinfo', function (Blueprint $table) {
            $table->id(); // Primary key (auto-incrementing)
            $table->unsignedBigInteger('customer_id'); // Foreign key column to reference 'customer' table

            // Store information fields
            $table->string('storename'); // Store name
            $table->string('contactno')->nullable(); // Contact number
            $table->string('region')->nullable(); // Region
            $table->string('province')->nullable(); // Province
            $table->string('city')->nullable(); // City
            $table->string('brgy')->nullable(); // Barangay
            $table->string('subdivision')->nullable(); // Subdivision
            $table->text('latitude')->nullable(); // Latitude as TEXT
            $table->text('longitude')->nullable(); // Longitude as TEXT
            $table->string('listype')->nullable(); // Type of listing
            $table->integer('length_stay')->nullable(); // Length of stay (e.g., in days)
            $table->text('remarks')->nullable(); // Remarks, allowing NULL values

            $table->timestamps(); // Automatically managed created_at and updated_at timestamps

            // Establish a foreign key relationship with the 'customer' table
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers') // Replace 'customers' with the correct table name if different
                ->onDelete('cascade'); // Set the foreign key to cascade deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storeinfo');
    }
}

