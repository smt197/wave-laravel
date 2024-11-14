<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseMonitor extends Command
{
    protected $signature = 'db:monitor';
    protected $description = 'Monitor database connection';

    public function handle()
    {
        try {
            DB::connection()->getPdo();
            return Command::SUCCESS;
        } catch (\Exception $e) {
            return Command::FAILURE;
        }
    }
}