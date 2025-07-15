<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use App\Utils\NotionDatabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Utils\SlackNotifier;

class NotifyReleaseSchedule extends Command
{
    protected $signature = 'slack:notifyReleaseSchedule {--channel=notifyTest}';

    protected $description = '開発ロードマップの進捗をSlack通知する';

    public function __construct(
        private Member $member
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $members = Member::select(['notion_id', 'name', 'slack_id'])->get();

        $releaseScheduleDatabase = new NotionDatabase(config('notion.api.releaseScheduleDatabaseUrl'));

        $releaseSchedules = $releaseScheduleDatabase
            ->setPayload($this->getReleaseSchedulePayload())
            ->get();

        $formattedReleaseSchedules = $releaseSchedules
            ->map(function ($releaseSchedule) use ($members) {
                $userId = $releaseSchedule['properties']['責任者']['people'][0]['id'] ?? null;
                return [
                    'url' => $releaseSchedule['url'],
                    'releaseDate' => Carbon::parse($releaseSchedule['properties']['リリース予定日']['date']['start'])->isoFormat('YYYY/MM/DD (ddd)'),
                    'slackId' =>  $members->firstWhere('notion_id', $userId)->slack_id ?? "",
                    'title' => $releaseSchedule['properties']['タイトル']['title'][0]['plain_text'] ?? "タイトルなし",
                    'status' => $releaseSchedule['properties']['ステータス']['status']['name'],
                ];
            })
            ->sortBy('releaseDate')
            ->map(function ($s) {
                $prefixIcon = $s['status'] === "Planned" ? ":rocket:" : ":white_check_mark:";
                return "{$prefixIcon} {$s['releaseDate']} <@{$s['slackId']}> - <{$s['url']}|{$s['title']}>";
            });

        $slackMessage = collect([
            "前後1週間のリリース一覧です。",
            "リリース予定を確認し、担当者はステータス更新や準備を行なってください。",
            PHP_EOL,
            "*<https://www.notion.so/wizleap/" . config('notion.api.releaseScheduleDatabaseUrl') . "|🎁リリーススケジュール>*",
            PHP_EOL,
            $formattedReleaseSchedules->join(PHP_EOL),
        ])->join(PHP_EOL);

        $slackNotifier = new SlackNotifier(config("slack.webhook.{$this->option('channel')}"));
        $slackNotifier
            ->setMessage($slackMessage)
            ->setAppName('リリーススケジュール')
            ->setIconEmoji(':rocket:')
            ->send();
    }

    private function getReleaseSchedulePayload()
    {
        // リリース予定日 日付 1週間前から1週間後まで
        $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(7)->format('Y-m-d');
        return [
            "filter" => [
                "and" => [
                    [
                        "property" => "リリース予定日",
                        "date" => [
                            "on_or_after" => $startDate
                        ]
                    ],
                    [
                        "property" => "リリース予定日",
                        "date" => [
                            "on_or_before" => $endDate
                        ]
                    ]
                ],
            ],
        ];
    }
}
