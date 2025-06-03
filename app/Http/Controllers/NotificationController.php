<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Console\Command;
use App\Console\Commands\NotifyDevelopPoint;
use App\Console\Commands\NotifyRoadmap;
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
}
