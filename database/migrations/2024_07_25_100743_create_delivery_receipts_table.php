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
            $table->date('date');
            $table->unsignedInteger('inbound_id');
            $table->string('customer_name');
            $table->string('generated_by');
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('bad_orders', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('amount_due', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->timestamps();

            $table->index('inbound_id');

        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_receipts');
    }
}
