<?php

namespace App\Http\Controllers;

use App\Console\Commands\NotifyDevelopPoint;
use App\Console\Commands\NotifyRoadmap;
use App\Console\Commands\NotifyReleaseSchedule;
use App\Console\Commands\NotifySimpleDevelopmentPoint;
use App\Console\Commands\NotifySimpleReleaseSchedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = config('slack.notifications');
        return response()->json($notifications);
    }

    public function send(Request $request)
    {
        $class = collect(config('slack.notifications'))
            ->first(fn($notification) => $notification['key'] === $request->key)['class'];

        Artisan::call($class, ['--channel' => $request->channel]);
    }
}
