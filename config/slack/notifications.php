<?php

use App\Console\Commands\NotifyDevelopPoint;
use App\Console\Commands\NotifyRoadmap;
use App\Console\Commands\NotifyReleaseSchedule;
use App\Console\Commands\NotifySimpleDevelopmentPoint;
use App\Console\Commands\NotifySimpleReleaseSchedule;

return [
    [
        'title' => '開発ポイント進捗通知',
        'key' => 'engineerDevPoint',
        'channel' => 'engineerDevPoint',
        'class' => NotifyDevelopPoint::class,
    ],
    [
        'title' => 'ロードマップ進捗通知',
        'key' => 'engineerRoadmap',
        'channel' => 'engineerRelease',
        'class' => NotifyRoadmap::class,
    ],
    [
        'title' => 'リリーススケジュール進捗通知',
        'key' => 'engineerRelease',
        'channel' => 'engineerRelease',
        'class' => NotifyReleaseSchedule::class,
    ],
    [
        'title' => 'シンプル開発ポイント進捗通知',
        'key' => 'engineerDevPointSimple',
        'channel' => 'timesNakajima',
        'class' => NotifySimpleDevelopmentPoint::class,
    ],
    [
        'title' => 'シンプルロードマップ進捗通知',
        'key' => 'engineerRoadmapSimple',
        'channel' => 'timesNakajima',
        'class' => NotifySimpleReleaseSchedule::class,
    ],
];
