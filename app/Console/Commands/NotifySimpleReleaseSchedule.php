<?php

namespace App\Console\Commands;

use App\Models\Member;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Utils\NotionDatabase;

class NotifySimpleReleaseSchedule extends Command
{
    protected $signature = 'slack:notifySimpleReleaseSchedule {--channel=notifyTest}';

    protected $description = 'シンプルなリリーススケジュールをSlack通知する';

    public function __construct(
        private Member $member
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->notifySimpleRoadmap();
    }

    private function notifySimpleRoadmap()
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
                // 30文字以上は切って3点
                $shortTitle = (mb_strlen($title) > 20
                    ? mb_substr($title, 0, 20) . "…"
                    : $title) ?? "タイトルなし";

                $status = $releaseSchedule['properties']['Status']['select']['name'] ?? "不明";

                $releaseDate = CarbonImmutable::parse($releaseSchedule['properties']['リリース日']['date']['start']);

                return [
                    'url' => $releaseSchedule['url'],
                    'releaseDate' => $releaseDate->startOfDay()->isPast()
                        ? $releaseDate->isoFormat('*M/DD (ddd)*')
                        : $releaseDate->isoFormat('M/DD (ddd)'),
                    'userName' => $releaseDate->startOfDay()->isPast()
                        ? "*{$members->firstWhere('notion_id',$userId)->name}*"
                        : $members->firstWhere('notion_id', $userId)->name ?? "Unknown",
                    'title' => $releaseDate->startOfDay()->isPast()
                        ? "*{$shortTitle}*"
                        : $shortTitle,
                    'status' => $status,
                    'statusIcon' => $this->getStatusIcon($status),
                    'userPrefixIcon' => $releaseDate->endOfDay()->isPast()
                        ? ":warning:"
                        : ":bust_in_silhouette:",
                    'isToday' => $releaseDate->startOfDay()->isPast(),
                ];
            })
            ->sortBy('releaseDate');

        // 本日のリリース件数をカウント
        $todayReleaseCount = $formattedReleaseSchedules->filter(fn($schedule) => $schedule['isToday'])->count();

        // メインブロックを生成
        $mainPayloads = $this->generateSimpleMainReleaseSchedulePayload($todayReleaseCount);

        // リリーススケジュールアイテムのブロックを生成
        $itemPayloads = $this->generateSimpleReleaseScheduleItemPayload($formattedReleaseSchedules);

        // メインブロックの中にアイテムブロックを追加
        array_splice($mainPayloads['blocks'], 1, 0, $itemPayloads);

        // Slackに通知
        $headers = ['Content-type' => 'application/json'];
        $url = collect(config('slack.channels'))
            ->first(fn($channel) =>
            $channel['key'] === $this->option('channel'))['webhookUrl'];
        $response = Http::withHeaders($headers)->post($url, $mainPayloads);

        // 失敗時に再度通知
        if ($response->status() >= 300) {
            $failedContents = config('slack.template.releaseSchedule.failed');
            Http::withHeaders($headers)->post($url, $failedContents);
        }
    }

    private function getReleaseSchedulePayload()
    {
        // リリース予定日 日付 1週間前から1週間後まで
        $startDate = CarbonImmutable::now()->subDays(10)->startOfDay()->format('Y-m-d');
        $endDate = CarbonImmutable::now()->addDays(3)->endOfDay()->format('Y-m-d');
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
                    ],
                    [
                        "property" => "Product",
                        "select" => [
                            "does_not_equal" => "セキュリティ"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "select" => [
                            "does_not_equal" => "リリース済"
                        ]
                    ],
                ],
            ],
        ];
    }

    private function generateSimpleMainReleaseSchedulePayload(int $todayReleaseCount): array
    {
        $mainTemplate = json_encode(config('slack.template.releaseSchedule.simpleMain'));

        $replace = [
            '%todayReleaseCount%' => $todayReleaseCount,
        ];

        return json_decode(strtr($mainTemplate, $replace), true);
    }

    private function generateSimpleReleaseScheduleItemPayload(Collection $releaseSchedules): array
    {
        return collect($releaseSchedules)
            ->flatMap(function ($schedule): array {
                $itemTemplate = json_encode(config('slack.template.releaseSchedule.simpleItem'));

                $replace = [
                    '%releaseDate%' => $schedule['releaseDate'],
                    '%userName%' => $schedule['userName'],
                    '%statusIcon%' => $schedule['statusIcon'],
                    '%userPrefixIcon%' => $schedule['userPrefixIcon'],
                    '%url%' => $schedule['url'],
                    '%title%' => $schedule['title'],
                ];

                return json_decode(strtr($itemTemplate, $replace), true);
            })
            ->toArray();
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
