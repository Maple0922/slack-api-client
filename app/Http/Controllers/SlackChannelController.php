<?php

namespace App\Http\Controllers;

class SlackChannelController extends Controller
{
    private const SLACK_CHANNEL_LINK_PREFIX = 'https://wizleap.slack.com/archives/';

    public function channels()
    {
        $channels = collect(config('slack.channels'))
            ->map(fn($channel) => [
                'key' => $channel['key'],
                'name' => $channel['name'],
                'link' => self::SLACK_CHANNEL_LINK_PREFIX . $channel['channelId'],
                'webhookUrl' => $channel['webhookUrl'],
            ]);

        return response()->json($channels);
    }
}
