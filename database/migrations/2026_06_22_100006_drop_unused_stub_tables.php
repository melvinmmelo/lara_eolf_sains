<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 hygiene: drop four dead stub tables. Each is empty (0 rows), has no
 * Eloquent model, and is referenced nowhere in the codebase except the legacy
 * database/eolf.sql dump (confirmed via `php artisan db:audit-stub-tables` and a
 * whole-repo grep). Three are bare id+timestamps scaffolding; temp_inbounds is
 * an abandoned session-cart table superseded by new_inbound_products.
 *
 * DATA-SAFE: up() drops a table only if it still has zero rows. If any row
 * exists in some environment, that table is skipped (never dropped), so no data
 * can be lost. down() recreates the tables from their captured definitions.
 */
return new class extends Migration
{
    /** @var array<string,string> table => CREATE TABLE body for down() */
    private array $tables = [
        'chart_of_accounts' => '(
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',

        'inventories' => '(
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',

        'purchases' => '(
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',

        'temp_inbounds' => '(
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `session` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `product_code` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
            `quantity` int NOT NULL,
            `status` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci',
    ];

    public function up(): void
    {
        foreach (array_keys($this->tables) as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            // Refuse to drop a table that unexpectedly holds data.
            if (DB::table($table)->count() > 0) {
                continue;
            }
            Schema::drop($table);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => $definition) {
            if (! Schema::hasTable($table)) {
                DB::statement("CREATE TABLE `{$table}` {$definition}");
            }
        }
    }
};
