<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 (additive): read-path indexes for hot queries that currently
 * table-scan against large tables.
 *
 *  - activity_log.created_at      : retention pruning + date-range filters
 *                                   (activity_log is the largest table, ~85MB).
 *  - delivery_receipts.inbound_id : every DR lookup joins back to its inbound;
 *                                   no index existed (only PK + branch_code).
 *  - inbounds.customer_id         : customer order history / per-customer reports.
 *  - inbounds.order_date          : dashboard + sales reports filter by date.
 *
 * Each index is created only if absent, so the migration is safe to run against
 * any environment regardless of which indexes already exist (idempotent).
 */
return new class extends Migration
{
    /** @var array<int, array{0:string,1:array<int,string>,2:string}> [table, columns, index_name] */
    private array $indexes = [
        ['activity_log', ['created_at'], 'activity_log_created_at_index'],
        ['delivery_receipts', ['inbound_id'], 'delivery_receipts_inbound_id_index'],
        ['inbounds', ['customer_id'], 'inbounds_customer_id_index'],
        ['inbounds', ['order_date'], 'inbounds_order_date_index'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as [$table, $columns, $name]) {
            if (! $this->indexExists($table, $name)) {
                Schema::table($table, function (Blueprint $t) use ($columns, $name) {
                    $t->index($columns, $name);
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as [$table, $columns, $name]) {
            if ($this->indexExists($table, $name)) {
                Schema::table($table, function (Blueprint $t) use ($name) {
                    $t->dropIndex($name);
                });
            }
        }
    }

    private function indexExists(string $table, string $name): bool
    {
        return DB::selectOne(
            'SELECT 1 FROM information_schema.statistics
             WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?
             LIMIT 1',
            [$table, $name]
        ) !== null;
    }
};
