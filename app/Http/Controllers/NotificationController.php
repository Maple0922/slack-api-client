<?php

namespace App\Http\Controllers;

use App\Console\Commands\NotifyDevelopPoint;
use App\Console\Commands\NotifyRoadmap;
use App\Console\Commands\NotifyReleaseSchedule;
use Illuminate\Support\Facades\Artisan;

class NotificationController extends Controller
{

    public function engineerDevPoint()
    {
        Artisan::call(NotifyDevelopPoint::class, [
            '--channel' => 'engineerDevPoint'
        ]);
    }

    public function engineerRoadmap()
    {
        Artisan::call(NotifyRoadmap::class, [
            '--channel' => 'engineerDevPoint'
        ]);
    }

    public function engineerRelease()
    {
        Artisan::call(NotifyReleaseSchedule::class, [
            '--channel' => 'engineerRelease'
        ]);
    }
}
