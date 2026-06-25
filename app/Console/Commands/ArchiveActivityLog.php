<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Phase 1 (retention, no data loss by default).
 *
 * The activity_log table grows unbounded (~85MB / 150k+ rows and climbing) and
 * has no retention. Spatie ships `activitylog:clean`, but that DELETES rows
 * outright. This command instead ARCHIVES rows older than the retention window
 * to a JSONL file under storage/app/activity-log-archives/ first, and only
 * deletes them when --prune is passed AND the archive was written successfully.
 *
 * Default (no --prune): export only, report how many WOULD be pruned. Nothing
 * is lost. Pair with the activity_log.created_at index (same phase) so both the
 * export scan and the delete are index-driven.
 *
 * Usage:
 *   php artisan activitylog:archive                 # archive rows older than retention, no delete
 *   php artisan activitylog:archive --days=90       # override retention window
 *   php artisan activitylog:archive --prune         # archive THEN delete the archived rows
 */
class ArchiveActivityLog extends Command
{
    protected $signature = 'activitylog:archive
                            {--days= : Retention window in days (default: config activitylog.delete_records_older_than_days)}
                            {--prune : Delete the archived rows after a successful export}
                            {--chunk=2000 : Rows per batch}';

    protected $description = 'Archive (and optionally prune) activity_log rows older than the retention window, without data loss';

    public function handle(): int
    {
        $table = config('activitylog.table_name', 'activity_log');
        $days = (int) ($this->option('days') ?: config('activitylog.delete_records_older_than_days', 365));
        $chunk = max(100, (int) $this->option('chunk'));

        if ($days <= 0) {
            $this->error('Retention window must be a positive number of days.');

            return self::FAILURE;
        }

        // Coarse, date-level cutoff. Deliberately day-granular so the UTC-vs-Manila
        // timestamp skew between prod and local is immaterial to what gets archived.
        $cutoff = Carbon::now()->subDays($days)->startOfDay();

        $base = fn () => DB::table($table)->where('created_at', '<', $cutoff);

        $total = (clone $base())->count();
        if ($total === 0) {
            $this->info("No {$table} rows older than {$cutoff->toDateString()} ({$days} days). Nothing to archive.");

            return self::SUCCESS;
        }

        $dir = 'activity-log-archives';
        $file = "{$dir}/{$table}-before-{$cutoff->toDateString()}-".now()->format('YmdHis').'.jsonl';
        Storage::makeDirectory($dir);
        $path = Storage::path($file);

        $this->info("Archiving {$total} {$table} rows older than {$cutoff->toDateString()} → storage/app/{$file}");

        $handle = fopen($path, 'wb');
        if ($handle === false) {
            $this->error("Could not open archive file for writing: {$path}");

            return self::FAILURE;
        }

        $written = 0;
        $maxId = 0;
        $bar = $this->output->createProgressBar($total);

        (clone $base())->orderBy('id')->chunkById($chunk, function ($rows) use ($handle, &$written, &$maxId, $bar) {
            foreach ($rows as $row) {
                fwrite($handle, json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL);
                $written++;
                $maxId = max($maxId, (int) $row->id);
                $bar->advance();
            }
        });

        fflush($handle);
        fclose($handle);
        $bar->finish();
        $this->newLine();

        if ($written !== $total) {
            $this->warn("Wrote {$written} of {$total} expected rows (table may have grown during export). Archive kept; not pruning.");

            return self::FAILURE;
        }

        $this->info("Archive complete: {$written} rows → storage/app/{$file} (".$this->humanSize($path).')');

        if (! $this->option('prune')) {
            $this->comment("Dry archive only. Re-run with --prune to delete the {$written} archived rows.");

            return self::SUCCESS;
        }

        // Prune only what we archived: rows under the cutoff with id <= the max
        // id we wrote. Nothing inserted after the export can fall in this range.
        $this->warn("Pruning {$written} archived rows (id <= {$maxId}, created_at < {$cutoff->toDateString()})...");
        $deleted = 0;
        do {
            $n = DB::table($table)
                ->where('created_at', '<', $cutoff)
                ->where('id', '<=', $maxId)
                ->limit($chunk)
                ->delete();
            $deleted += $n;
        } while ($n > 0);

        $this->info("Pruned {$deleted} rows. Archive retained at storage/app/{$file}.");

        return self::SUCCESS;
    }

    private function humanSize(string $path): string
    {
        $bytes = max(0, (int) @filesize($path));
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;
        $i = min($i, count($units) - 1);

        return round($bytes / (1024 ** $i), 2).' '.$units[$i];
    }
}
