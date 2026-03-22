<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inbound_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_id')->constrained('inbounds')->cascadeOnDelete();
            $table->string('branch_code', 20)->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('store_id')->nullable()->index();
            $table->date('payment_date')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->nullable()->index();
            $table->string('reference_no', 191)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->timestamps();

            $table->index(['inbound_id', 'payment_date']);
        });

        DB::table('inbounds')
            ->whereNotNull('delivered_amount')
            ->where('delivered_amount', '>', 0)
            ->orderBy('id')
            ->chunkById(200, function ($inbounds) {
                $rows = [];
                $now = now();

                foreach ($inbounds as $inbound) {
                    $rows[] = [
                        'inbound_id' => $inbound->id,
                        'branch_code' => $inbound->branch_code,
                        'customer_id' => $inbound->customer_id,
                        'store_id' => $inbound->store_id,
                        'payment_date' => $inbound->order_date ? date('Y-m-d', strtotime($inbound->order_date)) : ($inbound->updated_at ? date('Y-m-d', strtotime($inbound->updated_at)) : null),
                        'amount' => $inbound->delivered_amount,
                        'payment_method' => $inbound->payment_type,
                        'reference_no' => $inbound->ref_no,
                        'remarks' => 'Backfilled from legacy inbounds aggregate payment fields. Historical split/sequence unknown.',
                        'created_by' => $inbound->user_id,
                        'updated_by' => $inbound->user_id,
                        'created_at' => $inbound->created_at ?? $now,
                        'updated_at' => $inbound->updated_at ?? $now,
                    ];
                }

                if (!empty($rows)) {
                    DB::table('inbound_payments')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        // Restore aggregate fields on inbounds from the payments ledger before dropping
        DB::table('inbound_payments')
            ->select('inbound_id', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('inbound_id')
            ->orderBy('inbound_id')
            ->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $latest = DB::table('inbound_payments')
                        ->where('inbound_id', $row->inbound_id)
                        ->latest('payment_date')
                        ->latest('id')
                        ->first();

                    DB::table('inbounds')->where('id', $row->inbound_id)->update([
                        'delivered_amount' => $row->total_paid,
                        'payment_type'     => $latest?->payment_method,
                        'ref_no'           => $latest?->reference_no,
                    ]);
                }
            });

        Schema::dropIfExists('inbound_payments');
    }
};
