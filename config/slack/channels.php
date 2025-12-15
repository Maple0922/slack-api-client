<?php

return [
    [
        'key' => 'engineerDevPoint',
        'name' => '50_engineer_dev_point',
        'channelId' => env('SLACK_CHANNEL_ID_ENGINEER_DEV_POINT'),
        'webhookUrl' => env('SLACK_ENGINEER_DEV_POINT_WEBHOOK_URL')
    ],
    [
        'key' => 'engineerGeneral',
        'name' => '50_engineer_general',
        'channelId' => env('SLACK_CHANNEL_ID_ENGINEER_GENERAL'),
        'webhookUrl' => env('SLACK_ENGINEER_GENERAL_WEBHOOK_URL')
    ],
    [
        'key' => 'engineerRoadmap',
        'name' => '51_engineer_roadmap',
        'channelId' => env('SLACK_CHANNEL_ID_ENGINEER_ROADMAP'),
        'webhookUrl' => env('SLACK_ENGINEER_ROADMAP_WEBHOOK_URL')
    ],
    [
        'key' => 'engineerRelease',
        'name' => '56_engineer_release',
        'channelId' => env('SLACK_CHANNEL_ID_ENGINEER_RELEASE'),
        'webhookUrl' => env('SLACK_ENGINEER_RELEASE_WEBHOOK_URL')
    ],
    [
        'key' => 'notifyTest',
        'name' => '999_notify_test',
        'channelId' => env('SLACK_CHANNEL_ID_NOTIFY_TEST'),
        'webhookUrl' => env('SLACK_NOTIFY_TEST_WEBHOOK_URL')
    ],
    [
        'key' => 'timesNakajima',
        'name' => '99_times_nakajima',
        'channelId' => env('SLACK_CHANNEL_ID_TIMES_NAKAJIMA'),
        'webhookUrl' => env('SLACK_TIMES_NAKAJIMA_WEBHOOK_URL')
    ]
];
