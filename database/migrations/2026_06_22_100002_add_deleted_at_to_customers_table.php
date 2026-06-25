<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 (additive): give `customers` a soft-delete column.
 *
 * CustomersController::destroyStore() currently HARD-deletes a customer when
 * their last store is removed ($customer->delete()). Because inbounds/bad-orders
 * keep the customer_id as an immutable snapshot reference, that hard delete is
 * what orphaned ~52 historical customer_ids. Adding deleted_at (and the
 * SoftDeletes trait on the model) makes future deletes non-destructive while
 * preserving the document snapshots that point back at the row.
 *
 * Purely additive: a nullable column. Existing rows get NULL (= not deleted),
 * so behaviour is unchanged for every current customer.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('customers', 'deleted_at')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'deleted_at')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
