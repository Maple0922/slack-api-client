<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SDsController extends Controller
{
    private array $slackChannelIds;

    private const REPLACE_MENTION_USERNAME = [
        "<@U03TB56BZ7H>" => "@jumpei ueda",
        "<@U02G19EPNLB>" => "@ai kanayama",
        "<@U038QAHQ9SR>" => "@kayoko ito",
        "<@U02FF7RUSQ7>" => "@akiko nitta",
        "<@U01U5Q2QQQ4>" => "@yui watanabe",
        "<@U414GC4S3>" => "@shohei tanikawa",
        "<@ULS98A2QZ>" => "@tomoyuki hada",
        "<@U040SGWBQNL>" => "@ritsuko himoto",
        "<@U040H7MRLE9>" => "@yuuka yamashita",
        "<@U03UN3CHF7S>" => "@eri onoda",
        "<@U40LXDRJT>" => "@kengo kitaku",
        "<@U021PFA41EZ>" => "@futo nakajima",
        "<@U01GYMNQ4E4>" => "@itsuki tabata",
        "<@U037FRKMATV>" => "@nana imura",
        "<@U04445TS1QQ>" => "@chiho suzuki",
        "<@U05N2EYAVT5>" => "@haruka nishino",
        "<@U02UZS2MCB1>" => "@hiroki nagai",
        "<@U06BAUS9A3Y>" => "@chinami ebihara",
        "<@U066DPLTE7R>" => "@kyohei mori",
        "<@U05K0KYC5V1>" => "@yuka futami",
        "<!subteam^S033HEA941W|@engineer_executive>" => "@engineer_executive",
    ];

    private const REPLACE_SEND_USERNAME = [
        "U03TB56BZ7H" => "@jumpei ueda",
        "U02G19EPNLB" => "@ai kanayama",
        "U038QAHQ9SR" => "@kayoko ito",
        "U02FF7RUSQ7" => "@akiko nitta",
        "U01U5Q2QQQ4" => "@yui watanabe",
        "U414GC4S3" => "@shohei tanikawa",
        "ULS98A2QZ" => "@tomoyuki hada",
        "U040SGWBQNL" => "@ritsuko himoto",
        "U040H7MRLE9" => "@yuuka yamashita",
        "U03UN3CHF7S" => "@eri onoda",
        "U40LXDRJT" => "@kengo kitaku",
        "U021PFA41EZ" => "@futo nakajima",
        "U01GYMNQ4E4" => "@itsuki tabata",
        "U037FRKMATV" => "@nana imura",
        "U04445TS1QQ" => "@chiho suzuki",
        "U05N2EYAVT5" => "@haruka nishino",
        "U02UZS2MCB1" => "@hiroki nagai",
        "U06BAUS9A3Y" => "@chinami ebihara",
        "U066DPLTE7R" => "@kyohei mori",
        "U05K0KYC5V1" => "@yuka futami"
    ];

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

        $weeks = collect(range(0, 10));

        return $weeks
            ->sortDesc()
            ->map(function ($week) use ($channel, $client, $endpoint, $headers) {
                $beforeWeeks = Carbon::today()->subWeeks($week);
                $startTime = $beforeWeeks->copy()->subDay(6);
                $endTime = $beforeWeeks->copy()->addDay(1)->subSecond();

                $query = http_build_query([
                    'channel' => $this->slackChannelIds[$channel],
                    'oldest' => $startTime->format('U'),
                    'latest' => $endTime->format('U'),
                ]);
                $requestUrl = "{$endpoint}?{$query}";
                $response = $client->get($requestUrl, ['headers' => $headers]);
                $body = $response->getBody();
                $count = collect(json_decode($body)->messages)
                    ->filter(fn ($message) => isset($message->user))
                    ->count();

                return [
                    'start' => $startTime->isoFormat('YYYY-MM-DD (ddd) HH:mm'),
                    'end' => $endTime->isoFormat('YYYY-MM-DD (ddd) HH:mm'),
                    'count' => $count
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
        $channel = "mc-sd-crm";

        $startTime = Carbon::today()->subDay(6);
        $endTime = Carbon::today()->addDay(1)->subSecond();

        $query = http_build_query([
            'channel' => $this->slackChannelIds[$channel],
            'oldest' => $startTime->format('U'),
            'latest' => $endTime->format('U'),
        ]);
        $requestUrl = "{$endpoint}?{$query}";
        $response = $client->get($requestUrl, ['headers' => $headers]);
        $body = $response->getBody();
        $messages = collect(json_decode($body)->messages);

        return $messages
            ->filter(fn ($message) => isset($message->user))
            ->map(function ($message) {
                $date = Carbon::parse($message->ts)->timezone("Asia/Tokyo")->format('Y-m-d');
                $time = Carbon::parse($message->ts)->timezone("Asia/Tokyo")->format('H:i:s');
                return [
                    'user' => $this->addStyleToUsername(strtr($message->user, self::REPLACE_SEND_USERNAME)),
                    'content' => $this->addStyleToContent(strtr($message->text, self::REPLACE_MENTION_USERNAME)),
                    'datetime' => "{$date} {$time}",
                    'date' => $date,
                    'time' => $time
                ];
            })
            ->sortBy('datetime')
            ->values();
    }

    private function addStyleToUsername(string $text): string
    {
        return collect(self::REPLACE_SEND_USERNAME)
            ->reduce(
                fn ($text, $username) => Str::replace(
                    $username,
                    "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>{$username}</span>",
                    $text
                ),
                $text
            );
    }

    private function addStyleToContent(string $text): string
    {
        return collect(self::REPLACE_MENTION_USERNAME)
            ->reduce(
                fn ($text, $username) => Str::replace(
                    $username,
                    "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>{$username}</span>",
                    $text
                ),
                $text
            );
    }
}
