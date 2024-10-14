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
        //
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
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has failed.');
        }
    }
}
