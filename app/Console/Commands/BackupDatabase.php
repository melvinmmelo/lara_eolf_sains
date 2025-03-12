<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Symfony\Component\Process\Process;


class BackupDatabase extends Command
{

    protected $signature = 'db:backup';
    protected $description = 'Backup the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create backup
        $filename = "backup-" . date("Y-m-d-H-i-s") . ".sql";
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/' . $filename)
        );

        $process = Process::fromShellCommandline($command);

        try {
            $process->mustRun();
            $this->info('The backup has been created successfully.');
            \Log::info('The backup has been created successfully.');

            // Delete old backups
            $this->cleanOldBackups();
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has failed.');
        }
    }

    /**
     * Delete backup files older than 30 days
     */
    private function cleanOldBackups()
    {
        $backupPath = storage_path('app/backups');
        $files = glob($backupPath . '/*.sql');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 30 * 24 * 60 * 60) { // 30 days in seconds
                    unlink($file);
                    $this->info('Deleted old backup: ' . basename($file));
                }
            }
        }
    }
}
