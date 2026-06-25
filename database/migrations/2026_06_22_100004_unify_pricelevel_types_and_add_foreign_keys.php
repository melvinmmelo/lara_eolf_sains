<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Phase 3: unify the price-level reference types and add enforced foreign keys
 * on the RELATIONAL (config) tables only. Document/snapshot tables (inbounds,
 * etc.) are deliberately left without FKs — their *_id columns are immutable
 * point-in-time references, by design.
 *
 * Changes:
 *  1. customers.pricelevel_id: varchar(5) -> BIGINT UNSIGNED NULL.
 *     Legacy '' / non-numeric values are normalized to NULL first (verified
 *     zero pricing impact — no branch has a default price level flagged).
 *  2. prices.pricelevel_id: int unsigned -> BIGINT UNSIGNED (match pricelevels.id).
 *  3. FK customers.pricelevel_id -> pricelevels.id  ON DELETE SET NULL.
 *     (deleting a level just unsets it on customers; the row is never deleted.)
 *  4. FK prices.pricelevel_id  -> pricelevels.id   ON DELETE RESTRICT.
 *  5. FK prices.p_code         -> product_types.code ON DELETE RESTRICT.
 *
 * All FKs use SET NULL / RESTRICT — never CASCADE — so no FK can ever delete a
 * row. All steps are guarded, so the migration is idempotent. Pre-verified:
 * 0 orphans on every relationship, collations match (utf8mb4_unicode_ci).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Normalize customers.pricelevel_id while it is still a string column.
        if (in_array($this->columnType('customers', 'pricelevel_id'), ['varchar', 'char'], true)) {
            DB::statement("UPDATE customers SET pricelevel_id = NULL
                           WHERE pricelevel_id = '' OR pricelevel_id NOT REGEXP '^[0-9]+$'");
            DB::statement('ALTER TABLE customers MODIFY pricelevel_id BIGINT UNSIGNED NULL');
        }

        // 2. Widen prices.pricelevel_id to match pricelevels.id (bigint unsigned).
        if ($this->columnType('prices', 'pricelevel_id') === 'int') {
            DB::statement('ALTER TABLE prices MODIFY pricelevel_id BIGINT UNSIGNED NOT NULL');
        }

        // 3-5. Foreign keys (each auto-creates its backing index if absent).
        if (! $this->foreignKeyExists('customers', 'fk_customers_pricelevel_id')) {
            DB::statement('ALTER TABLE customers
                ADD CONSTRAINT fk_customers_pricelevel_id FOREIGN KEY (pricelevel_id)
                REFERENCES pricelevels (id) ON DELETE SET NULL ON UPDATE CASCADE');
        }
        if (! $this->foreignKeyExists('prices', 'fk_prices_pricelevel_id')) {
            DB::statement('ALTER TABLE prices
                ADD CONSTRAINT fk_prices_pricelevel_id FOREIGN KEY (pricelevel_id)
                REFERENCES pricelevels (id) ON DELETE RESTRICT ON UPDATE CASCADE');
        }
        if (! $this->foreignKeyExists('prices', 'fk_prices_p_code')) {
            DB::statement('ALTER TABLE prices
                ADD CONSTRAINT fk_prices_p_code FOREIGN KEY (p_code)
                REFERENCES product_types (code) ON DELETE RESTRICT ON UPDATE CASCADE');
        }
    }

    public function down(): void
    {
        foreach ([
            ['customers', 'fk_customers_pricelevel_id'],
            ['prices', 'fk_prices_pricelevel_id'],
            ['prices', 'fk_prices_p_code'],
        ] as [$table, $fk]) {
            if ($this->foreignKeyExists($table, $fk)) {
                DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `$fk`");
            }
        }

        if ($this->columnType('prices', 'pricelevel_id') === 'bigint') {
            DB::statement('ALTER TABLE prices MODIFY pricelevel_id INT UNSIGNED NOT NULL');
        }
        // Revert type only; the '' -> NULL normalization is a one-way cleanup.
        if ($this->columnType('customers', 'pricelevel_id') === 'bigint') {
            DB::statement('ALTER TABLE customers MODIFY pricelevel_id VARCHAR(5) NULL');
        }
    }

    private function columnType(string $table, string $column): ?string
    {
        return DB::selectOne(
            'SELECT DATA_TYPE FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?',
            [$table, $column]
        )?->DATA_TYPE;
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        return DB::selectOne(
            'SELECT 1 FROM information_schema.table_constraints
             WHERE table_schema = DATABASE() AND table_name = ?
               AND constraint_name = ? AND constraint_type = \'FOREIGN KEY\' LIMIT 1',
            [$table, $constraint]
        ) !== null;
    }
};
