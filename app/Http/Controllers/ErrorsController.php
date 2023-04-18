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
            'crm-market-holder_egs' => env('SLACK_CHANNEL_ID_ERROR_CRM_MARKET_HOLDER_PRODUCTION_EGS'),
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
            'crm-market-holder_egs',
            'money-career'
        ]);

        $weeks = collect(range(0, 4));

        return $weeks
            ->sortDesc()
            ->map(function ($week) use ($channels, $client, $endpoint, $headers) {
                $beforeWeeks = Carbon::now()->subWeeks($week);

                $errors = $channels
                    ->flatMap(function ($channel) use ($client, $endpoint, $headers, $beforeWeeks) {
                        $query = http_build_query([
                            'channel' => $this->slackChannelIds[$channel],
                            'oldest' => strtotime('last thursday', $beforeWeeks->format('U')),
                            'latest' => strtotime('this wednesday', $beforeWeeks->format('U')),
                        ]);
                        $requestUrl = "{$endpoint}?{$query}";
                        $response = $client->get($requestUrl, ['headers' => $headers]);
                        $body = $response->getBody();
                        \Log::channel('single')->emergency($body);
                        $messages = collect(json_decode($body)->messages);
                        $formatMessages = $messages
                            ->sortBy('ts')
                            ->filter(fn ($message) => $message->username ?? $message->user === 'Laravel')
                            ->map(fn ($message) => date('Y-m-d', substr($message->ts, 0, 10)));

                        return [
                            Str::camel($channel) => $formatMessages->count()
                        ];
                    });


                return [
                    'week' => "{$beforeWeeks->subDay(6)->format('Y-m-d')} - {$beforeWeeks->addDay(6)->format('Y-m-d')}",
                    ...$errors->toArray(),
                    'total' => $errors->sum()
                ];
            })
            ->values();
    }
}
