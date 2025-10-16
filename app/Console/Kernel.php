<?php

namespace App\Console;

use App\Console\Commands\NotifySimpleDevelopmentPoint;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\NotifyDevelopPoint;
use App\Console\Commands\NotifyEngineerMtgOrder;
use App\Console\Commands\NotifyReleaseSchedule;
use App\Console\Commands\NotifySimpleReleaseSchedule;
use App\Console\Commands\AggregateDevelopPoint;
use App\Console\Commands\NotifyRoadmap;

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
        // エンジニア週次の順番 (水15:00)
        $schedule->command(NotifyEngineerMtgOrder::class, ['--channel' => 'engineerGeneral'])->weeklyOn(3, '15:00');

        // 開発ポイント進捗 (平日9:00, 21:00)
        $schedule->command(NotifyDevelopPoint::class, ['--channel' => 'engineerDevPoint'])->weekdays()->dailyAt('9:00');
        $schedule->command(NotifyDevelopPoint::class, ['--channel' => 'engineerDevPoint'])->weekdays()->dailyAt('21:00');

        // ロードマップ進捗　(平日9:00, 火18:00)
        $schedule->command(NotifyRoadmap::class, ['--channel' => 'engineerRelease'])->weekdays()->dailyAt('9:00');
        $schedule->command(NotifyRoadmap::class, ['--channel' => 'engineerRelease'])->weeklyOn(2, '18:00');

        // 個人用
        $schedule->command(NotifySimpleDevelopmentPoint::class, ['--channel' => 'timesNakajima'])->weekdays()->dailyAt('9:00');
        $schedule->command(NotifySimpleReleaseSchedule::class, ['--channel' => 'timesNakajima'])->weekdays()->dailyAt('9:01');

        // 開発ポイント集計
        $schedule->command(AggregateDevelopPoint::class)->weeklyOn(4, '0:00');
        $schedule->command(AggregateDevelopPoint::class)->weeklyOn(3, '8:30');
        $schedule->command(AggregateDevelopPoint::class)->weeklyOn(3, '9:00');
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
