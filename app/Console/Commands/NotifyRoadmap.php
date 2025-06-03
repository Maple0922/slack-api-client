<?php

namespace App\Console\Commands;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NotifyRoadmap extends Command
{
    protected $signature = 'slack:notifyRoadmap {--channel=notifyTest}';

    protected $description = '開発ロードマップの進捗をSlack通知する';

    public function __construct(
        private Member $member
    ) {
        parent::__construct();
    }

    public function handle()
    {
        // $members = Member::query()
        //     ->select('name')
        //     ->whereNot('name', 'Kengo Kitaku')
        //     ->get()
        //     ->shuffle()
        //     ->pluck('name')
        //     ->map(function ($name, int $key) {
        //         $index = $key + 1;
        //         return "{$index}. {$name}";
        //     })
        //     ->join('\n');

        // $template = config('slack.template.engineerMtgOrder.main');
        // $replace = [
        //     '%eol%' => '\n',
        //     '%members%' => $members,
        // ];

        // $payload = json_decode(strtr(json_encode($template), $replace), true);

        // $headers = ['Content-type' => 'application/json'];
        // $url = config("slack.webhook.{$this->option('channel')}");

        // $response = Http::withHeaders($headers)->post($url, $payload);
        // if ($response->status() >= 300) {
        //     $failedContents = config('slack.template.engineerMtgOrder.failed');
        //     Http::withHeaders($headers)->post($url, $failedContents);
        // }
    }
}
