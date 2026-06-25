<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 4: a relational projection of the inbounds.products JSON blob, one row
 * per order line. The JSON blob remains the source of truth; this table is a
 * DERIVED, rebuildable mirror (see `php artisan inbound:project-lines`) that
 * makes per-product / per-branch / per-date sales reporting expressible in SQL
 * with indexes instead of scanning and decoding JSON in PHP.
 *
 * branch_code and order_date are denormalized from the parent inbound because
 * they are immutable after creation (safe to copy). Order status is NOT
 * denormalized — reports join back to inbounds for status to avoid drift.
 *
 * inbound_lines is genuinely owned by its inbound, so the FK cascades on delete
 * (the only place in this schema a CASCADE FK is appropriate — unlike the
 * snapshot/document tables).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inbound_lines')) {
            return;
        }

        Schema::create('inbound_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_id')->constrained('inbounds')->cascadeOnDelete();
            $table->string('branch_code', 15)->nullable()->index();
            $table->dateTime('order_date')->nullable()->index();
            $table->string('product_code', 191)->nullable();      // e.g. BC_MF
            $table->string('ptype_code', 191)->nullable()->index(); // e.g. BC
            $table->string('description', 255)->nullable();
            $table->string('unit', 50)->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);     // quantity * price
            $table->unsignedInteger('line_order')->nullable();    // the JSON "order"
            $table->timestamps();

            $table->index(['branch_code', 'order_date']); // common report filter
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_lines');
    }
};
