<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Carbon\Carbon;

class SDsController extends Controller
{
    private array $slackChannelIds;

    public function __construct()
    {
        $this->slackChannelIds = [
            'mc-sd-crm' => env('SLACK_CHANNEL_ID_MC_SD_CRM')
        ];
    }

    public function count()
    {
        $client = new Client();
        $endpoint = 'https://slack.com/api/conversations.history';
        $headers = [
            "Authorization" => "Bearer " . env('SLACK_TOKEN')
        ];

        $channel = "mc-sd-crm";

        $weeks = collect(range(0, 4));

        return $weeks->map(function ($week) use ($channel, $client, $endpoint, $headers) {
            $beforeWeeks = Carbon::now()->subWeeks($week);
            $query = http_build_query([
                'channel' => $this->slackChannelIds[$channel],
                'oldest' => strtotime('last wednesday', $beforeWeeks->format('U')),
                'latest' => strtotime('this wednesday', $beforeWeeks->format('U')),
            ]);
            $requestUrl = "{$endpoint}?{$query}";
            $response = $client->get($requestUrl, ['headers' => $headers]);
            $body = $response->getBody();
            $count = collect(json_decode($body)->messages)->count();

            return [
                'week' => "{$beforeWeeks->format('Y-m-d')} - {$beforeWeeks->subDay(6)->format('Y-m-d')}",
                'count' => $count
            ];
        });
    }

    public function list()
    {
        return [];
    }
}
