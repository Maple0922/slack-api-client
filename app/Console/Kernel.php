<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 平日のみ
        $schedule->command('slack:notifyDevelopPoint --channel=engineerDevPoint')->weekdays()->dailyAt('09:00');
        $schedule->command('slack:notifyDevelopPoint --channel=engineerDevPoint')->weekdays()->dailyAt('15:00');
        $schedule->command('slack:notifyDevelopPoint --channel=engineerDevPoint')->weekdays()->dailyAt('21:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
