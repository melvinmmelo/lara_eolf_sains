<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Phase 3 pre-flight / data migration. The customers.pricelevel_id column is a
 * varchar(5) that stores numeric price-level ids, but legacy rows hold the empty
 * string '' (654 rows) instead of NULL for "no explicit level". The app already
 * writes NULL for unset levels (CustomersController: `?: null`), so '' is purely
 * legacy noise. This normalizes '' (and any non-numeric value) to NULL so the
 * column can be converted to BIGINT UNSIGNED and given a real foreign key.
 *
 * Safe by design: verified there is no branch with a flagged default price level
 * (pricelevels.is_default), so `resolvedPriceLevelId()` returns the same falsy
 * "no level" for '' and NULL alike — zero pricing impact. The command re-checks
 * this and warns if a default level now exists.
 *
 * Usage:
 *   php artisan customers:normalize-pricelevel          # dry run (report only)
 *   php artisan customers:normalize-pricelevel --apply  # write '' / non-numeric -> NULL
 *
 * The Phase 3 migration also performs this normalization inline, so running this
 * command is optional — it exists for visibility and to decouple the data fix
 * from the schema change if preferred.
 */
class NormalizeCustomerPriceLevel extends Command
{
    protected $signature = 'customers:normalize-pricelevel {--apply : Apply the change (default is a dry run)}';

    protected $description = "Normalize customers.pricelevel_id '' / non-numeric values to NULL (pre-flight for the bigint+FK conversion)";

    public function handle(): int
    {
        // Match rows that should become NULL: empty string or anything not a
        // positive integer. NULL rows are already correct and left untouched.
        $offending = DB::table('customers')
            ->whereNotNull('pricelevel_id')
            ->whereRaw("(pricelevel_id = '' OR pricelevel_id NOT REGEXP '^[0-9]+$')");

        $count = (clone $offending)->count();

        $this->line('Distinct non-canonical pricelevel_id values:');
        $samples = (clone $offending)
            ->select('pricelevel_id', DB::raw('COUNT(*) as n'))
            ->groupBy('pricelevel_id')->get();
        foreach ($samples as $s) {
            $this->line(sprintf('  [%s] -> NULL  (%d rows)', $s->pricelevel_id === '' ? '<empty>' : $s->pricelevel_id, $s->n));
        }
        if ($count === 0) {
            $this->info('Nothing to normalize — every pricelevel_id is NULL or numeric.');
        }

        // Re-confirm the no-pricing-impact assumption.
        $defaultLevels = DB::table('pricelevels')->where('is_default', 1)->count();
        if ($defaultLevels > 0) {
            $this->warn("NOTE: {$defaultLevels} price level(s) are flagged is_default. Normalizing '' -> NULL will make those customers fall back to the branch default level. Review before applying.");
        } else {
            $this->info('No branch has a default price level flagged — normalization has no pricing effect.');
        }

        if (! $this->option('apply')) {
            $this->comment($count > 0
                ? "Dry run. Re-run with --apply to set {$count} rows to NULL."
                : 'Dry run.');

            return self::SUCCESS;
        }

        if ($count === 0) {
            return self::SUCCESS;
        }

        $updated = $offending->update(['pricelevel_id' => null]);
        $this->info("Normalized {$updated} customers.pricelevel_id values to NULL.");

        return self::SUCCESS;
    }
}
