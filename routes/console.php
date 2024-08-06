<?php

use App\Jobs\UpdatePostStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Commands
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console Schedule
|--------------------------------------------------------------------------
|
| Below you may define your scheduled tasks, including console commands
| or system commands. These tasks will be run automatically when due
| using Laravel's built-in "schedule:run" Artisan console command.
|
*/

$schedule = app(\Illuminate\Console\Scheduling\Schedule::class);

// Define the schedule intervals and their methods
$intervals = [
    'everyFiveMinutes' => [],
    'dailyAt' => ['00:00']
];

// Iterate over the intervals to schedule the command
foreach ($intervals as $method => $parameters) {
    if (!empty($parameters)) {
        foreach ($parameters as $parameter) {
            $schedule->command('app:check:posts-status')->$method($parameter);
        }
    } else {
        $schedule->command('app:check:posts-status')->$method();
    }
}
