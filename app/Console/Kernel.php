<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\NotifyDevelopPoint;
use App\Console\Commands\NotifyEngineerMtgOrder;
use App\Console\Commands\NotifyReleaseSchedule;

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
        // エンジニア週次の順番
        $schedule->command(NotifyEngineerMtgOrder::class, ['--channel' => 'engineerGeneral'])->weeklyOn(3, '15:00');

        // 開発ポイント進捗
        $schedule->command(NotifyDevelopPoint::class, ['--channel' => 'engineerDevPoint'])->weekdays()->dailyAt('09:00');
        $schedule->command(NotifyDevelopPoint::class, ['--channel' => 'engineerDevPoint'])->weekdays()->dailyAt('15:00');
        $schedule->command(NotifyDevelopPoint::class, ['--channel' => 'engineerDevPoint'])->weekdays()->dailyAt('21:00');

        // リリーススケジュール
        $schedule->command(NotifyReleaseSchedule::class, ['--channel' => 'engineerRelease'])->weekdays()->dailyAt('10:00');
        $schedule->command(NotifyReleaseSchedule::class, ['--channel' => 'engineerRelease'])->weekdays()->dailyAt('16:00');
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
