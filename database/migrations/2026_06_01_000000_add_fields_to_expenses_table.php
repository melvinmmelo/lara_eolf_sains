<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('branch_code')->nullable()->index()->after('id');
            $table->date('expense_date')->nullable()->after('branch_code');
            $table->string('category')->nullable()->after('expense_date');
            $table->string('particulars')->nullable()->after('category');
            $table->string('payee')->nullable()->after('particulars');
            $table->decimal('amount', 12, 2)->default(0)->after('payee');
            $table->string('payment_method')->nullable()->after('amount');
            $table->string('reference_no')->nullable()->after('payment_method');
            $table->text('remarks')->nullable()->after('reference_no');
            $table->unsignedBigInteger('created_by')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'branch_code',
                'expense_date',
                'category',
                'particulars',
                'payee',
                'amount',
                'payment_method',
                'reference_no',
                'remarks',
                'created_by',
            ]);
        });
    }
};
