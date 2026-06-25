<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Phase 2 (read-only pre-flight). Reports the exact monetary drift that
 * converting the float/double money columns to DECIMAL(15,2) would introduce,
 * so the change can be reviewed before — and verified after — the migration.
 *
 * For each column it prints:
 *   rows        — total rows
 *   sum_float   — SUM of the values as currently stored (float arithmetic)
 *   sum_decimal — SUM after rounding each value to 2 dp (what DECIMAL will hold)
 *   net_drift   — sum_decimal - sum_float (the total money change; ~0 == safe)
 *   worst_row   — largest single-row change abs(round(v,2) - v)
 *   >0.5cent    — count of rows that move by more than half a cent (genuine
 *                 sub-cent data, vs. float representation noise)
 *
 * A near-zero net_drift with worst_row ~0.005 and few/zero >0.5cent rows means
 * the conversion only cleans up float noise and is safe.
 *
 * Usage: php artisan db:audit-money
 */
class AuditMoneyColumns extends Command
{
    protected $signature = 'db:audit-money';

    protected $description = 'Report monetary drift from converting float/double money columns to DECIMAL(15,2)';

    /** @var array<int, array{0:string,1:string}> [table, column] */
    public const COLUMNS = [
        ['bad_orders', 'amount'],
        ['bad_orders', 'price'],
        ['inbounds', 'bo_amount'],
        ['inbounds', 'delivered_amount'],
        ['inbounds', 'discount'],
        ['materials_inventories', 'amount'],
        ['new_inbound_products', 'price'],
        ['new_temp_bad_orders', 'price'],
        ['order_slips', 'total_amount'],
        ['prices', 'p_price'],
    ];

    public function handle(): int
    {
        $rows = [];
        $maxNetDrift = 0.0;

        foreach (self::COLUMNS as [$table, $col]) {
            $q = DB::table($table)->selectRaw("
                COUNT(*) AS rows_n,
                COALESCE(SUM(CAST(`$col` AS DECIMAL(30,10))), 0) AS sum_float,
                COALESCE(SUM(ROUND(`$col`, 2)), 0) AS sum_decimal,
                COALESCE(MAX(ABS(ROUND(`$col`, 2) - `$col`)), 0) AS worst_row,
                COALESCE(SUM(CASE WHEN ABS(ROUND(`$col`, 2) - `$col`) > 0.005 THEN 1 ELSE 0 END), 0) AS over_half_cent
            ")->first();

            $netDrift = (float) $q->sum_decimal - (float) $q->sum_float;
            $maxNetDrift = max($maxNetDrift, abs($netDrift));

            $rows[] = [
                "$table.$col",
                number_format((int) $q->rows_n),
                number_format((float) $q->sum_float, 4),
                number_format((float) $q->sum_decimal, 2),
                sprintf('%+.4f', $netDrift),
                sprintf('%.6f', (float) $q->worst_row),
                (int) $q->over_half_cent,
            ];
        }

        $this->table(
            ['column', 'rows', 'sum_float', 'sum_decimal', 'net_drift', 'worst_row', '>0.5cent'],
            $rows
        );

        $this->newLine();
        if ($maxNetDrift < 1.0) {
            $this->info(sprintf('Largest per-column net drift: %.4f — within float-noise tolerance. Conversion is safe.', $maxNetDrift));
        } else {
            $this->warn(sprintf('Largest per-column net drift: %.4f — review the columns above before converting.', $maxNetDrift));
        }

        return self::SUCCESS;
    }
}
