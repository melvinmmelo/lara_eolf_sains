<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Phase 3 hygiene: product_variants has no PRIMARY KEY — only a UNIQUE index on
 * `code` (MySQL reports the column as "PRI" because it is a NOT NULL unique key,
 * but no real primary key exists). Every other code-keyed table (product_types)
 * uses `code` as its PRIMARY KEY, and the ProductVariant model already declares
 * `protected $primaryKey = 'code'`.
 *
 * Promote `code` to the real PRIMARY KEY and drop the now-redundant unique index.
 * No surrogate column, no model change, no code references an `id`. Tiny table,
 * trivial rewrite.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! $this->hasPrimaryKey('product_variants')) {
            DB::statement('ALTER TABLE product_variants ADD PRIMARY KEY (code)');
        }
        if ($this->indexExists('product_variants', 'product_variants_code_unique')) {
            DB::statement('ALTER TABLE product_variants DROP INDEX product_variants_code_unique');
        }
    }

    public function down(): void
    {
        if (! $this->indexExists('product_variants', 'product_variants_code_unique')) {
            DB::statement('ALTER TABLE product_variants ADD UNIQUE KEY product_variants_code_unique (code)');
        }
        if ($this->hasPrimaryKey('product_variants')) {
            DB::statement('ALTER TABLE product_variants DROP PRIMARY KEY');
        }
    }

    private function hasPrimaryKey(string $table): bool
    {
        return DB::selectOne(
            "SELECT 1 FROM information_schema.table_constraints
             WHERE table_schema = DATABASE() AND table_name = ?
               AND constraint_type = 'PRIMARY KEY' LIMIT 1",
            [$table]
        ) !== null;
    }

    private function indexExists(string $table, string $name): bool
    {
        return DB::selectOne(
            'SELECT 1 FROM information_schema.statistics
             WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1',
            [$table, $name]
        ) !== null;
    }
};
