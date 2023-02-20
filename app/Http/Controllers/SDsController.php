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

        return $weeks
            ->sortDesc()
            ->map(function ($week) use ($channel, $client, $endpoint, $headers) {
                $beforeWeeks = Carbon::now()->subWeeks($week);
                $query = http_build_query([
                    'channel' => $this->slackChannelIds[$channel],
                    'oldest' => strtotime('last wednesday', $beforeWeeks->format('U')),
                    'latest' => strtotime('this wednesday', $beforeWeeks->format('U')),
                ]);
                $requestUrl = "{$endpoint}?{$query}";
                $response = $client->get($requestUrl, ['headers' => $headers]);
                $body = $response->getBody();
                $count = collect(json_decode($body)->messages)
                    ->filter(fn ($message) => isset($message->user))
                    ->count();

                return [
                    'week' => "{$beforeWeeks->subDay(6)->format('Y-m-d')} - {$beforeWeeks->addDay(6)->format('Y-m-d')}",
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

        $query = http_build_query([
            'channel' => $this->slackChannelIds[$channel],
            'oldest' => strtotime('last wednesday', Carbon::today()->format('U')),
            'latest' => strtotime('this wednesday', Carbon::today()->format('U')),
        ]);
        $requestUrl = "{$endpoint}?{$query}";
        $response = $client->get($requestUrl, ['headers' => $headers]);
        $body = $response->getBody();
        $messages = collect(json_decode($body)->messages);

        $replaceMentionUsername = [
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
            "<!subteam^S033HEA941W|@engineer_executive>" => "@engineer_executive",
        ];

        $replaceSendUsername = [
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
            "U04445TS1QQ" => "@chiho suzuki"
        ];

        $replaceHTML = [
            "@jumpei ueda" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@jumpei ueda</span>",
            "@kengo kitaku" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@kengo kitaku</span>",
            "@futo nakajima" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@futo nakajima</span>",
            "@itsuki tabata" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@itsuki tabata</span>",
            "@jumpei ueda" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@jumpei ueda</span>",
            "@engineer_executive" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@engineer_executive</span>",
            "@ai kanayama" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@ai kanayama</span>",
            "@kayoko ito" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@kayoko ito</span>",
            "@akiko nitta" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@akiko nitta</span>",
            "@yui watanabe" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@yui watanabe</span>",
            "@shohei tanikawa" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@shohei tanikawa</span>",
            "@tomoyuki hada" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@tomoyuki hada</span>",
            "@ritsuko himoto" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@ritsuko himoto</span>",
            "@yuuka yamashita" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@yuuka yamashita</span>",
            "@eri onoda" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@eri onoda</span>",
            "@nana imura" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@nana imura</span>",
            "@chiho suzuki" => "<span class='px-1 font-weight-bold d-inline-block rounded' style='color: blue; background-color: skyblue'>@chiho suzuki</span>",
        ];

        return $messages
            ->filter(fn ($message) => isset($message->user))
            ->map(fn ($message) => [
                'user' => strtr(strtr($message->user, $replaceSendUsername), $replaceHTML),
                'content' => strtr(strtr($message->text, $replaceMentionUsername), $replaceHTML),
                'datetime' => Carbon::parse($message->ts)->timezone("Asia/Tokyo")->format('Y-m-d H:i:s'),
                'date' => Carbon::parse($message->ts)->timezone("Asia/Tokyo")->format('Y-m-d'),
                'time' => Carbon::parse($message->ts)->timezone("Asia/Tokyo")->format('H:i:s'),
            ])
            ->sortBy('datetime')
            ->values();
    }
}
