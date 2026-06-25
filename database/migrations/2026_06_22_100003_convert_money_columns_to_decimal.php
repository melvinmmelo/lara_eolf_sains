<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Phase 2: convert the float/double money columns on the sales path to
 * DECIMAL(15,2) so amounts are stored exactly (no binary-float drift).
 *
 * Verified safe by `php artisan db:audit-money` beforehand: zero rows carry
 * genuine sub-cent values, the worst single-row change is < half a cent (float
 * representation noise), and the largest per-column net drift is ~1.6 cents
 * across 11k inbounds — the DECIMAL value is the more correct one.
 *
 * Idempotent: each column is altered only if still float/double (up) or decimal
 * (down), so re-running is a no-op. NOTE for production: the inbounds ALTER
 * rewrites a ~74MB table and briefly locks it — run in a maintenance window.
 */
return new class extends Migration
{
    /**
     * [table, column, up DDL fragment, down DDL fragment].
     * Definitions mirror the originals' nullability/defaults exactly.
     *
     * @var array<int, array{0:string,1:string,2:string,3:string}>
     */
    private array $columns = [
        ['bad_orders', 'amount', 'DECIMAL(15,2) NOT NULL', 'DOUBLE NOT NULL'],
        ['bad_orders', 'price', 'DECIMAL(15,2) NOT NULL', 'DOUBLE NOT NULL'],
        ['inbounds', 'bo_amount', 'DECIMAL(15,2) NULL DEFAULT NULL', 'FLOAT NULL DEFAULT NULL'],
        ['inbounds', 'delivered_amount', 'DECIMAL(15,2) NULL DEFAULT NULL', 'FLOAT NULL DEFAULT NULL'],
        ['inbounds', 'discount', 'DECIMAL(15,2) NULL DEFAULT 0.00', 'FLOAT(15,2) NULL DEFAULT 0.00'],
        ['materials_inventories', 'amount', 'DECIMAL(15,2) NOT NULL', 'FLOAT(15,2) NOT NULL'],
        ['new_inbound_products', 'price', 'DECIMAL(15,2) NOT NULL', 'FLOAT NOT NULL'],
        ['new_temp_bad_orders', 'price', 'DECIMAL(15,2) NOT NULL', 'DOUBLE NOT NULL'],
        ['order_slips', 'total_amount', 'DECIMAL(15,2) NOT NULL', 'FLOAT NOT NULL'],
        ['prices', 'p_price', 'DECIMAL(15,2) NOT NULL', 'FLOAT NOT NULL'],
    ];

    public function up(): void
    {
        foreach ($this->columns as [$table, $col, $upDef]) {
            if (in_array($this->dataType($table, $col), ['float', 'double'], true)) {
                DB::statement("ALTER TABLE `$table` MODIFY `$col` $upDef");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->columns as [$table, $col, , $downDef]) {
            if ($this->dataType($table, $col) === 'decimal') {
                DB::statement("ALTER TABLE `$table` MODIFY `$col` $downDef");
            }
        }
    }

    private function dataType(string $table, string $column): ?string
    {
        $row = DB::selectOne(
            'SELECT DATA_TYPE FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?',
            [$table, $column]
        );

        return $row?->DATA_TYPE;
    }
};
