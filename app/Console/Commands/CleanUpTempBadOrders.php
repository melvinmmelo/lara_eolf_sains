<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TempBadOrder;
use Carbon\Carbon;

class CleanUpTempBadOrders extends Command
{
    protected $signature = 'cleanup:tempbadorders';
    protected $description = 'Clean up old entries from temp_bad_orders table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $threshold = Carbon::now()->subHours(1); // Adjust as needed
        TempBadOrder::where('created_at', '<', $threshold)->delete();
        $this->info('Old entries cleaned up from temp_bad_orders table.');
    }
}
