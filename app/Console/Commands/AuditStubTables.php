<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Phase 3 hygiene (read-only). Reports empty tables and whether anything in
 * app/ still references them, so genuinely-dead stub tables can be dropped by a
 * human decision rather than guessed at.
 *
 * Deliberately does NOT drop anything: several "stub" tables (deliveries,
 * company_details, employees, sales) have models or controller references even
 * at 0 rows, and dropping a referenced table breaks the app at runtime — a far
 * worse outcome than an unused empty table. Dropping is left to an explicit,
 * reviewed migration.
 *
 * Usage: php artisan db:audit-stub-tables
 */
class AuditStubTables extends Command
{
    protected $signature = 'db:audit-stub-tables';

    protected $description = 'Report empty tables and whether app code still references them (no changes made)';

    /**
     * Framework / package-managed tables. They are used by vendor code (queue,
     * cache, auth, Spatie permission/activitylog), not app/, so a code scan
     * would wrongly flag them. Never list these as drop candidates.
     */
    private const FRAMEWORK_TABLES = [
        'migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
        'sessions', 'password_reset_tokens', 'password_resets', 'personal_access_tokens',
        'permissions', 'roles', 'model_has_permissions', 'model_has_roles',
        'role_has_permissions', 'activity_log',
    ];

    public function handle(): int
    {
        $tables = DB::table('information_schema.tables')
            ->whereRaw('table_schema = DATABASE()')
            ->where('table_type', 'BASE TABLE')
            ->selectRaw('table_name AS table_name')
            ->pluck('table_name');

        $codeFiles = $this->codeFiles(base_path('app'));
        $modelTables = $this->modelTableNames(base_path('app/Models'));

        $rows = [];
        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            if ($count > 0) {
                continue; // only interested in empty tables
            }

            if (in_array($table, self::FRAMEWORK_TABLES, true)) {
                $rows[] = [$table, $count, 'framework/package', 'keep (framework)'];

                continue;
            }

            // Referenced if a quoted literal appears in app/, OR an Eloquent
            // model maps to it (explicit $table or naming convention).
            $referenced = in_array($table, $modelTables, true)
                || $this->referencedInCode($table, $codeFiles);

            $rows[] = [
                $table,
                $count,
                $referenced ? 'referenced' : 'no refs found',
                $referenced ? 'keep (review)' : 'drop candidate',
            ];
        }

        if (empty($rows)) {
            $this->info('No empty tables found.');

            return self::SUCCESS;
        }

        $this->table(['empty_table', 'rows', 'code_reference', 'verdict'], $rows);
        $this->newLine();
        $this->comment('This audit makes no changes. Review "drop candidate" rows, then drop them via an explicit migration if truly unused.');

        return self::SUCCESS;
    }

    /** @return string[] absolute paths of php files under app/ */
    private function codeFiles(string $dir): array
    {
        $files = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Resolve the table name each Eloquent model maps to — either an explicit
     * `protected $table = '...'` or the class-name convention — so a model with
     * no string literal (e.g. Delivery -> deliveries) is still detected.
     *
     * @return string[]
     */
    private function modelTableNames(string $modelsDir): array
    {
        $tables = [];
        if (! is_dir($modelsDir)) {
            return $tables;
        }
        foreach (glob($modelsDir.'/*.php') as $path) {
            $contents = (string) @file_get_contents($path);
            if (preg_match('/protected\s+\$table\s*=\s*[\'"]([^\'"]+)[\'"]/', $contents, $m)) {
                $tables[] = $m[1];

                continue;
            }
            if (preg_match('/class\s+(\w+)/', $contents, $m)) {
                $tables[] = Str::snake(Str::pluralStudly($m[1]));
            }
        }

        return array_unique($tables);
    }

    private function referencedInCode(string $table, array $files): bool
    {
        // Match the table name as a quoted string ('table' or "table"), which is
        // how it would appear in $table declarations, DB::table(), joins, etc.
        $needles = ["'{$table}'", "\"{$table}\""];
        foreach ($files as $path) {
            $contents = @file_get_contents($path);
            if ($contents === false) {
                continue;
            }
            foreach ($needles as $needle) {
                if (str_contains($contents, $needle)) {
                    return true;
                }
            }
        }

        return false;
    }
}
