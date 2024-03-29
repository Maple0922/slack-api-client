<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ErrorsController extends Controller
{
    public function __construct()
    {
        $this->slackChannelIds = [
            'crm-admin' => env('SLACK_CHANNEL_ID_ERROR_CRM_ADMIN_PRODUCTION'),
            'crm-expert' => env('SLACK_CHANNEL_ID_ERROR_CRM_EXPERT_PRODUCTION'),
            'crm-bot' => env('SLACK_CHANNEL_ID_ERROR_CRM_BOT_PRODUCTION'),
            'crm-market-holder' => env('SLACK_CHANNEL_ID_ERROR_CRM_MARKET_HOLDER_PRODUCTION'),
            'money-career' => env('SLACK_CHANNEL_ID_ERROR_MONEY_CAREER_PRODUCTION')
        ];
    }

    public function count()
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
            'crm-market-holder',
            'money-career'
        ]);

        $weeks = collect(range(0, 10));

        return $weeks
            ->sortDesc()
            ->map(function ($week) use ($channels, $client, $endpoint, $headers) {
                $beforeWeeks = Carbon::today()->subWeeks($week);
                $start = Carbon::parse(
                    strtotime('last wednesday', $beforeWeeks->format('U'))
                    )->addDay()->startOfDay();

                $end = Carbon::parse(
                    strtotime('this tuesday', $beforeWeeks->format('U'))
                    )->addDay()->endOfDay();

                $errors = $channels
                    ->flatMap(function ($channel) use ($client, $endpoint, $headers, $start, $end) {
                        $query = http_build_query([
                            'channel' => $this->slackChannelIds[$channel],
                            'oldest' => $start->format('U'),
                            'latest' => $end->format('U'),
                        ]);
                        $requestUrl = "{$endpoint}?{$query}";
                        $response = $client->get($requestUrl, ['headers' => $headers]);
                        $body = $response->getBody();
                        $messages = collect(json_decode($body)->messages);
                        $formatMessages = $messages
                            ->sortBy('ts')
                            ->filter(fn ($message) => $message->username ?? optional($message)->user === 'Laravel')
                            ->map(fn ($message) => date('Y-m-d', substr($message->ts, 0, 10)));

                        return [
                            Str::camel($channel) => $formatMessages->count()
                        ];
                    });

                return [
                    'start' => $start->isoFormat('YYYY-MM-DD (ddd) HH:mm'),
                    'end' => $end->isoFormat('YYYY-MM-DD (ddd) HH:mm'),
                    ...$errors->toArray(),
                    'total' => $errors->sum()
                ];
            })
            ->values();
    }

    public function list()
    {
        $client = new Client();
        $endpoint = 'https://slack.com/api/conversations.history';
        $headers = [
            "Authorization" => "Bearer " . env('SLACK_TOKEN')
        ];

        $channels = [
            "crm-admin",
            "crm-expert",
            "crm-bot",
            "crm-market-holder",
            "money-career"
        ];

        return collect($channels)
            ->flatMap(function (string $channel) use ($client, $endpoint, $headers) {
                $query = http_build_query([
                    'channel' => $this->slackChannelIds[$channel],
                    'oldest' => strtotime('last thursday', Carbon::today()->format('U')),
                    'latest' => strtotime('this wednesday', Carbon::today()->format('U')),
                ]);

                $requestUrl = "{$endpoint}?{$query}";
                $response = $client->get($requestUrl, ['headers' => $headers]);
                $body = $response->getBody();
                $messages = collect(json_decode($body)->messages);

                return $messages
                    ->filter(fn ($message) => isset($message->attachments))
                    ->map(fn ($message) => [
                        'channel' => $channel,
                        'content' => $message->attachments[0]->text ?? \Log::channel('single')->emergency(print_r($message->attachments[0], true)),
                        'datetime' => date('Y-m-d', substr($message->ts, 0, 10)),
                    ])
                    ->groupBy('content')
                    ->map(fn ($message) => [
                        'channel' => $message->first()['channel'],
                        'content' => $message->first()['content'],
                        'count' => $message->count(),
                        'datetime' => $message->last()['datetime'],
                    ])
                    ->values();
            })
            ->sortBy('channel')
            ->sortByDesc('count')
            ->values();
    }
}
