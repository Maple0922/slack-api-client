<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Utils\NotionDatabase;
use App\Utils\SlackNotifier;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyReleaseSchedule extends Command
{
    protected $signature = 'slack:notifyReleaseSchedule {--channel=notifyTest}';

    protected $description = '開発ロードマップからリリーススケジュールの進捗をSlack通知する';

    public function __construct(
        private Member $member
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $members = Member::select(['notion_id', 'name', 'slack_id'])->get();

        $roadmapDatabase = new NotionDatabase(config('notion.api.roadmapDatabaseUrl'));

        $releaseSchedules = $roadmapDatabase
            ->setPayload($this->getReleaseSchedulePayload())
            ->get();

        $formattedReleaseSchedules = $releaseSchedules
            ->map(function ($releaseSchedule) use ($members) {
                $userId = $releaseSchedule['properties']['責任者']['people'][0]['id'] ?? null;

                $title = $releaseSchedule['properties']['Name']['title'][0]['plain_text'];
                // 20文字以上は切って3点
                $shortTitle = mb_strlen($title) > 30
                    ? mb_substr($title, 0, 30) . "…"
                    : $title;

                return [
                    'url' => $releaseSchedule['url'],
                    'releaseDate' => Carbon::parse($releaseSchedule['properties']['リリース日']['date']['start'])->isoFormat('YYYY/MM/DD (ddd)'),
                    'slackId' =>  $members->firstWhere('notion_id', $userId)->slack_id ?? "",
                    'title' => $shortTitle ?? "タイトルなし",
                    'status' => $releaseSchedule['properties']['Status']['select']['name'] ?? "不明",
                ];
            })
            ->sortBy('releaseDate')
            ->map(function ($s) {
                $prefixIcon = $this->getStatusIcon($s['status']);
                return "{$s['releaseDate']} [ {$prefixIcon} *{$s['status']}* ] <@{$s['slackId']}> - <{$s['url']}|*{$s['title']}*>";
            });

        $slackMessage = collect([
            "直近のリリース予定です。",
            "担当のロードマップを確認し、ステータス更新や準備を行なってください。",
            "遅れがある場合はスレッドに理由を記載の上、リリース予定日を更新してください。",
            PHP_EOL,
            "*<https://www.notion.so/wizleap/" . config('notion.api.roadmapDatabaseUrl') . "|🥳開発ロードマップ>*",
            PHP_EOL,
            $formattedReleaseSchedules->join(PHP_EOL),
        ])->join(PHP_EOL);

        $slackNotifier = new SlackNotifier(config("slack.webhook.{$this->option('channel')}"));
        $slackNotifier
            ->setMessage($slackMessage)
            ->setAppName('開発ロードマップ')
            ->setIconEmoji(':rocket:')
            ->send();
    }

    private function getReleaseSchedulePayload()
    {
        // リリース予定日 日付 1週間前から1週間後まで
        $startDate = Carbon::now()->subDays(3)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(10)->format('Y-m-d');
        return [
            "filter" => [
                "and" => [
                    [
                        "property" => "リリース日",
                        "date" => [
                            "on_or_after" => $startDate
                        ]
                    ],
                    [
                        "property" => "リリース日",
                        "date" => [
                            "on_or_before" => $endDate
                        ]
                    ]
                ],
            ],
        ];
    }

    private function getStatusIcon($status)
    {
        return match ($status) {
            'リリース済' => ":white_check_mark:",
            'QA対応中' => ":rocket:",
            '開発中' => ":construction:",
            '開発スタンバイ' => ":construction:",
            default => ":question:",
        };
    }
}
