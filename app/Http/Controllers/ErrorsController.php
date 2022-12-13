<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Carbon\Carbon;

class ErrorsController extends Controller
{
    public function __construct()
    {
        $this->slackChannelIds = [
            'crm-admin' => env('SLACK_CHANNEL_ID_ERROR_CRM_ADMIN_PRODUCTION'),
            'crm-expert' => env('SLACK_CHANNEL_ID_ERROR_CRM_EXPERT_PRODUCTION'),
            'crm-bot' => env('SLACK_CHANNEL_ID_ERROR_CRM_BOT_PRODUCTION'),
            'money-career' => env('SLACK_CHANNEL_ID_ERROR_MONEY_CAREER_PRODUCTION')
        ];
    }

    public function errors()
    {
        $client = new Client();
        $endpoint = 'https://slack.com/api/conversations.history';
        $headers = [
            "Authorization" => "Bearer " . env('SLACK_TOKEN')
        ];

        $channels = collect([
            'crm-admin',
            'crm-expert',
            'crm-bot',
            'money-career'
        ]);

        $weeks = collect(range(0, 4));

        return $weeks->map(function ($week) use ($channels, $client, $endpoint, $headers) {
            $beforeWeeks = Carbon::now()->subWeeks($week);

            $errors = $channels
                ->map(function ($channel) use ($client, $endpoint, $headers, $beforeWeeks) {
                    $query = http_build_query([
                        'channel' => $this->slackChannelIds[$channel],
                        'oldest' => strtotime('last wednesday', $beforeWeeks->format('U')),
                        'latest' => strtotime('this wednesday', $beforeWeeks->format('U')),
                    ]);
                    $requestUrl = "{$endpoint}?{$query}";
                    $response = $client->get($requestUrl, ['headers' => $headers]);
                    $body = $response->getBody();
                    $messages = collect(json_decode($body)->messages);
                    $formatMessages = $messages
                        ->sortBy('ts')
                        ->filter(fn ($message) => $message->username ?? $message->user === 'Laravel')
                        ->map(fn ($message) => date('Y-m-d', substr($message->ts, 0, 10)));

                    return [
                        'channel' => $channel,
                        'count' => $formatMessages->count()
                    ];
                });


            return [
                'week' => "{$beforeWeeks->format('Y-m-d')} - {$beforeWeeks->subDay(6)->format('Y-m-d')}",
                'errors' => $errors
            ];
        });
    }
}
