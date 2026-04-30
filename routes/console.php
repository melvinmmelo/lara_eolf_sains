<?php
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

// Schedule::call(function () {
//     DB::statement('INSERT INTO item_master_data_history SELECT * FROM item_master_data');
// })->dailyAt('23:59');

// Schedule::command('backup:clean')->daily()->at('01:00');
// Schedule::command('backup:run')->daily()->at('01:30');

Schedule::command('db:backup')->daily()->at('01:00');
Schedule::command('activitylog:clean')->daily()->at('01:13');
