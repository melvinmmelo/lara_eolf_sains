<?php

// database/migrations/xxxx_xx_xx_create_delivery_receipts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryReceiptsTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code');
            $table->date('date');
            $table->unsignedInteger('inbound_id')->unique();
            $table->string('customer_name');
            $table->string('generated_by');
            $table->string('status')->nullable();
            $table->dateTime('printed_date')->nullable();
            $table->timestamps();

            $table->index('branch_code');

        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_receipts');
    }
}
