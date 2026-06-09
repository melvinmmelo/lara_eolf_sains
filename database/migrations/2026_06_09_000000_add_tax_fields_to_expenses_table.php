<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * BIR-compliance fields for petty-cash / official-receipt bookkeeping:
     * the payee's tax identity (taxpayer type, TIN, registered address) and the
     * petty cash voucher number the disbursement was drawn against.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('taxpayer_type')->nullable()->after('payee');
            $table->string('tin')->nullable()->after('taxpayer_type');
            $table->string('payee_address')->nullable()->after('tin');
            $table->string('petty_cash_no')->nullable()->after('reference_no');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'taxpayer_type',
                'tin',
                'payee_address',
                'petty_cash_no',
            ]);
        });
    }
};
