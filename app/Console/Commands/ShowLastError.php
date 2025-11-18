<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowLastError extends Command
{
    protected $signature = 'log:last {lines=50}';
    protected $description = 'Show last error from laravel.log';

    public function handle()
    {
        $lines = $this->argument('lines');
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            $this->error('Log file not found!');
            return 1;
        }

        $this->info(shell_exec("tail -n {$lines} {$logFile}"));
        return 0;
    }
}