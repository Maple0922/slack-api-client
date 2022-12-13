<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class DeleteMessages extends Command
{
    public function __construct()
    {
        $this->slackChannelIds = [
            'mcSdCancel' => env('SLACK_CHANNEL_ID_MC_SD_CANCEL'),
        ];
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'メッセージを削除する';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $existMessage = true;
        while ($existMessage) {

            $client = new Client();
            $endpoint = 'https://slack.com/api/conversations.history';
            $query = http_build_query([
                'channel' => $this->slackChannelIds['mcSdCancel'],
                'oldest' => Carbon::today()->format('U')
            ]);

            $headers = [
                "Authorization" => "Bearer " . env('SLACK_TOKEN')
            ];

            $requestUrl = "{$endpoint}?{$query}";
            $response = $client->get($requestUrl, ['headers' => $headers]);
            $body = $response->getBody();
            $messages = collect(json_decode($body)->messages);

            if ($messages->isEmpty()) {
                $existMessage = false;
                break;
            }

            $messages
                ->sortByDesc('ts')
                ->map(fn ($message) => [
                    'user' => $message->username ?? $message->user,
                    'ts' => $message->ts,
                    'created' => Carbon::parse($message->ts)->timezone('Asia/Tokyo')->format('Y-m-d H:i:s')
                ])
                ->each(function ($message) {
                    $client = new Client();
                    $endpoint = 'https://slack.com/api/chat.delete';
                    $query = http_build_query([
                        'channel' => $this->slackChannelIds['mcSdCancel'],
                        'ts' => $message['ts'],
                    ]);

                    $headers = [
                        "Authorization" => "Bearer " . env('SLACK_TOKEN')
                    ];

                    $requestUrl = "{$endpoint}?{$query}";
                    $res = $client->post($requestUrl, ['headers' => $headers]);

                    $this->info("{$message['created']}のメッセージを削除しました");
                    sleep(1);
                });
        }
    }
}
