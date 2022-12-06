<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class CountMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:messages {week=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'メッセージをカウントする';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $week = $this->argument('week');
        $start = Carbon::now()->addWeeks($week)->format('U');
        $client = new Client();
        $endpoint = 'https://slack.com/api/conversations.history';
        $query = http_build_query([
            'channel' => env('SLACK_CHANNEL_ID_TIMES_NAKAJIMA'),
            'oldest' => strtotime('last wednesday', $start),
            'latest' => strtotime('this wednesday', $start),
        ]);

        $headers = [
            "Authorization" => "Bearer " . env('SLACK_TOKEN')
        ];

        $requestUrl = "{$endpoint}?{$query}";
        $response = $client->get($requestUrl, ['headers' => $headers]);
        $body = $response->getBody();
        $messages = collect(json_decode($body)->messages);

        $formatMessages = $messages
            ->sortBy('ts')
            ->map(fn ($message) => [
                'user' => $message->username ?? $message->user,
                'text' => $message->text,
                'ts' => date('Y-m-d H:i:s', substr($message->ts, 0, 10))
            ])
            ->each(fn ($message) => \Log::channel('single')->emergency($message['ts'] . ' ' . $message['user'] . ' ' . $message['text']));;
    }
}
