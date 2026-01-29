<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix prices.p_code to store product type codes instead of full product codes
     * Example: Convert "SC_RR" to "SC", "N3.6L_VNL" to "N3.6L"
     */
    public function up(): void
    {
        // Step 1: Delete rows that would conflict with existing product-type codes
        // Example: If 'SC' already exists at pricelevel 2, delete 'SC_BM' and 'SC_RR' at pricelevel 2
        DB::statement("
            DELETE p1 FROM prices p1
            INNER JOIN prices p2
                ON p1.pricelevel_id = p2.pricelevel_id
                AND p2.p_code NOT LIKE '%\\_%'
                AND p2.p_code = SUBSTRING_INDEX(p1.p_code, '_',
                    CHAR_LENGTH(p1.p_code) - CHAR_LENGTH(REPLACE(p1.p_code, '_', '')))
            WHERE p1.p_code LIKE '%\\_%'
        ");

        // Step 2: Delete duplicates among remaining underscore codes
        // Keep only the record with highest id for each (pricelevel_id, extracted_p_code) combination
        DB::statement("
            DELETE p1 FROM prices p1
            INNER JOIN prices p2
                ON p1.pricelevel_id = p2.pricelevel_id
                AND SUBSTRING_INDEX(p1.p_code, '_', CHAR_LENGTH(p1.p_code) - CHAR_LENGTH(REPLACE(p1.p_code, '_', '')))
                  = SUBSTRING_INDEX(p2.p_code, '_', CHAR_LENGTH(p2.p_code) - CHAR_LENGTH(REPLACE(p2.p_code, '_', '')))
                AND p1.id < p2.id
            WHERE p1.p_code LIKE '%\\_%'
        ");

        // Step 3: Now safely update p_code to extract product type
        DB::statement("
            UPDATE prices
            SET p_code = SUBSTRING_INDEX(p_code, '_',
                CHAR_LENGTH(p_code) - CHAR_LENGTH(REPLACE(p_code, '_', ''))
            )
            WHERE p_code LIKE '%\\_%'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * Cannot fully reverse - would need backup table
     */
    public function down(): void
    {
        // Cannot reverse this migration without a backup table
        // If you need to rollback, restore from database backup
    }
};
