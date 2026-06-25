<?php

namespace App\Console\Commands;

use App\Models\Inbound;
use App\Models\InboundLine;
use App\Services\InboundLineProjector;
use Illuminate\Console\Command;

/**
 * Phase 4: backfill / rebuild / verify the inbound_lines projection from the
 * inbounds.products JSON (the source of truth).
 *
 *   php artisan inbound:project-lines            # rebuild lines for every inbound
 *   php artisan inbound:project-lines --fresh    # truncate inbound_lines, then rebuild all
 *   php artisan inbound:project-lines --inbound=123   # rebuild just one order
 *   php artisan inbound:project-lines --verify   # no writes: report any drift vs JSON
 *
 * Rebuild is idempotent (each inbound's lines are replaced). Run it after any
 * bulk/raw change to inbounds.products; normal Eloquent saves stay in sync via
 * the Inbound observer.
 */
class ProjectInboundLines extends Command
{
    protected $signature = 'inbound:project-lines
                            {--inbound= : Only process this inbound id}
                            {--fresh : Truncate inbound_lines before rebuilding}
                            {--verify : Report drift between projection and JSON without writing}
                            {--chunk=500 : Inbounds processed per batch}';

    protected $description = 'Backfill/rebuild/verify the inbound_lines projection from inbounds.products JSON';

    public function handle(InboundLineProjector $projector): int
    {
        $chunk = max(50, (int) $this->option('chunk'));

        $query = Inbound::query()->orderBy('id');
        if ($single = $this->option('inbound')) {
            $query->where('id', (int) $single);
        }

        $total = (clone $query)->count();
        if ($total === 0) {
            $this->warn('No matching inbounds.');

            return self::SUCCESS;
        }

        if ($this->option('verify')) {
            return $this->verify($query, $projector, $total);
        }

        if ($this->option('fresh')) {
            if ($this->option('inbound')) {
                $this->warn('--fresh ignored when --inbound is set.');
            } else {
                $this->warn('Truncating inbound_lines...');
                InboundLine::truncate();
            }
        }

        $this->info("Projecting lines for {$total} inbound(s)...");
        $bar = $this->output->createProgressBar($total);
        $orders = 0;
        $lines = 0;
        $emptyOrInvalid = 0;

        $query->chunkById($chunk, function ($inbounds) use ($projector, $bar, &$orders, &$lines, &$emptyOrInvalid) {
            foreach ($inbounds as $inbound) {
                $written = $projector->project($inbound);
                $orders++;
                $lines += $written;
                if ($written === 0) {
                    $emptyOrInvalid++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Done. {$orders} inbounds → {$lines} line rows. ({$emptyOrInvalid} had no/invalid products and produced 0 lines.)");

        return self::SUCCESS;
    }

    private function verify($query, InboundLineProjector $projector, int $total): int
    {
        $this->info("Verifying {$total} inbound(s) against their projection (no writes)...");
        $bar = $this->output->createProgressBar($total);
        $drift = [];

        $query->chunkById(500, function ($inbounds) use ($projector, $bar, &$drift) {
            foreach ($inbounds as $inbound) {
                $jsonTotal = $projector->jsonLineTotal($inbound);
                $projTotal = (float) InboundLine::where('inbound_id', $inbound->id)->sum('line_total');
                if (abs($jsonTotal - $projTotal) > 0.01) {
                    $drift[] = [$inbound->id, number_format($jsonTotal, 2), number_format($projTotal, 2)];
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        if (empty($drift)) {
            $this->info('No drift: every inbound_lines projection matches its JSON blob.');

            return self::SUCCESS;
        }

        $this->warn(count($drift).' inbound(s) drifted from their JSON. Re-run without --verify to rebuild:');
        $this->table(['inbound_id', 'json_total', 'projected_total'], array_slice($drift, 0, 50));
        if (count($drift) > 50) {
            $this->line('... '.(count($drift) - 50).' more.');
        }

        return self::FAILURE;
    }
}
