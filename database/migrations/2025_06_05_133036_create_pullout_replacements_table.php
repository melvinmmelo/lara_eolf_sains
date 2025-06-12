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
        Schema::create('pullout_replacements', function (Blueprint $table) {
            $table->id();
            $table->string('pof_no')->unique();
            $table->string('customer_name');
            $table->string('address');
            $table->string('sales_agent');
            $table->date('date');
            
            // Pull-out details
            $table->string('pullout_model_serial_no')->nullable();
            $table->string('pullout_degic_no')->nullable();
            $table->string('pullout_pr_no')->nullable();
            $table->string('pullout_cv_no')->nullable();
            $table->string('pullout_rs_no')->nullable();
            $table->decimal('refund_deposit', 10, 2)->nullable();
            
            // Replaced details
            $table->string('replaced_model_serial_no')->nullable();
            $table->string('replaced_degic_no')->nullable();
            $table->string('replaced_agreement_no')->nullable();
            $table->string('replaced_fods_no')->nullable();
            $table->string('replaced_lock_key')->nullable();
            $table->string('replaced_signage')->nullable();
            
            // Freezer status
            $table->boolean('defective_compressor')->default(false);
            $table->boolean('not_cooling')->default(false);
            $table->boolean('stop_selling')->default(false);
            $table->boolean('system_leak')->default(false);
            $table->boolean('condemned')->default(false);
            $table->boolean('return_to_supplier')->default(false);
            
            // Additional details
            $table->text('remarks')->nullable();
            $table->string('prepared_by');
            $table->string('noted_by');
            $table->string('pullout_by')->nullable();
            $table->string('customer_signature')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pullout_replacements');
    }
};
