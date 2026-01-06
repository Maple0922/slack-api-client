<?php

namespace App\Console\Commands;

use App\Models\DevelopPoint;
use App\Models\Member;
use App\Utils\NotionDatabase;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriodImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AggregateDevelopPoint extends Command
{
    protected $signature = 'aggregate:developPoint {--startDate=} {--endDate=}';

    protected $description = '開発ポイントを集計する';

    public function __construct(
        private Member $member,
        private DevelopPoint $developPoint
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $startDate = $this->option('startDate')
            ? CarbonImmutable::parse($this->option('startDate'))
            : CarbonImmutable::today()->subWeeks(2);
        $endDate = $this->option('endDate')
            ? CarbonImmutable::parse($this->option('endDate'))
            : CarbonImmutable::today();

        $members = $this->member
            ->with('offDates')
            ->where('is_valid', 1)
            ->get();

        $inReviewDates = collect(
            CarbonPeriodImmutable::create(
                $startDate,
                $endDate
            )
        )
            ->reverse()
            ->filter(fn(CarbonImmutable $date) => $date->isTuesday())
            ->values();

        $this->log("📊 集計対象日: ");
        $inReviewDates->each(
            fn(CarbonImmutable $date) =>
            $this->log($date->format('🗓️ Y/m/d'))
        );

        $inReviewDates
            ->each(function (CarbonImmutable $inReviewDate) use ($members) {
                $this->log("🚀 {$inReviewDate->format('Y/m/d')} のデータを取得します");
                $backlogRecords = $this->getBacklogRecords($inReviewDate, $members);
                $this->log("👥 {$backlogRecords->unique('member.notion_id')->count()}人 / {$backlogRecords->count()}件 / {$backlogRecords->sum('point')}ポイント");
                $this->aggregateDevelopPoint($inReviewDate, $backlogRecords, $members);
                $this->log("✅ {$inReviewDate->format('Y/m/d')} のデータを集計しました");
            });
    }

    private function getBacklogRecords(
        CarbonImmutable $inReviewDate,
        Collection $members
    ): Collection {
        $parentDatabase = new NotionDatabase(config('notion.api.parentDatabaseUrl'));
        $backlogDatabase = new NotionDatabase(config('notion.api.backlogDatabaseUrl'));

        $parentPayload = config('notion.payload.parent');
        $parentPayload['filter']['rich_text']['equals'] = $inReviewDate->format('Y/m/d');

        $parentResponse = $parentDatabase->setPayload($parentPayload)->get();
        $parentPageId = $parentResponse[0]['id'];

        $backlogPayload = config('notion.payload.backlog.aggregate');
        $backlogPayload['filter']['and'][0]['relation']['contains'] = $parentPageId;
        $backlogResponse = $backlogDatabase->setPayload($backlogPayload)->get();

        return $backlogResponse
            ->map(fn($backlog) => [
                'member' => $members->first(fn($member) => $member->notion_id === ($backlog['properties']['Manager']['people'][0]['id'] ?? null)),
                'point' => $backlog['properties']['Point']['number'] ?? 0,
            ])
            ->filter(
                fn($backlog) =>
                $backlog['point'] > 0 &&
                    isset($backlog['member']) &&
                    $members->pluck('notion_id')->contains($backlog['member']['notion_id'])
            )
            ->values();
    }

    private function aggregateDevelopPoint(
        CarbonImmutable $inReviewDate,
        Collection $backlogRecords,
        Collection $members
    ): void {
        $now = CarbonImmutable::now();

        // backlogRecordsに含まれるメンバーのIDを取得
        $backlogMemberIds = $backlogRecords
            ->pluck('member.notion_id')
            ->filter()
            ->unique()
            ->values();

        // backlogRecordsに含まれるメンバーのdevelopPointを処理
        $backlogRecords
            ->groupBy('member.notion_id')
            ->map(function ($backlogRecords) use ($inReviewDate, $now) {
                $member = $backlogRecords->first()['member'];
                $offDates = $member->offDates->pluck('date')->map(fn($date) => $date->format('Y-m-d'));
                $businessDayCount = collect([0, 1, 4, 5, 6])
                    ->reject(fn($day) => $offDates->contains(
                        $inReviewDate
                            ->subDays($day)
                            ->format('Y-m-d')
                    ))
                    ->count();

                $targetPoint = $member->target_point * $businessDayCount / 5;

                return [
                    'member_notion_id' => $member['notion_id'],
                    'point' => $backlogRecords->sum('point'),
                    'target' => $targetPoint,
                    'in_review_date' => $inReviewDate,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->each(function ($developPoint) {
                $existing = $this->developPoint->where([
                    'member_notion_id' => $developPoint['member_notion_id'],
                    'in_review_date' => $developPoint['in_review_date']
                ])->first();

                if ($existing) {
                    // レコードが存在する場合、targetを除いて更新
                    $existing->update([
                        'point' => $developPoint['point'],
                        'updated_at' => $developPoint['updated_at'],
                    ]);
                } else {
                    // レコードが存在しない場合、全てのフィールド（targetを含む）で作成
                    $this->developPoint->create($developPoint);
                }
            });

        // backlogRecordsに含まれていないメンバーのdevelopPointを作成
        $members
            ->reject(fn(Member $member) => $backlogMemberIds->contains($member->notion_id))
            ->each(function (Member $member) use ($inReviewDate, $now) {
                $offDates = $member->offDates->pluck('date')->map(fn($date) => $date->format('Y-m-d'));
                $businessDayCount = collect([0, 1, 4, 5, 6])
                    ->reject(fn($day) => $offDates->contains(
                        $inReviewDate
                            ->subDays($day)
                            ->format('Y-m-d')
                    ))
                    ->count();

                $targetPoint = $member->target_point * $businessDayCount / 5;

                $developPoint = [
                    'member_notion_id' => $member->notion_id,
                    'point' => 0,
                    'target' => $targetPoint,
                    'in_review_date' => $inReviewDate,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $existing = $this->developPoint->where([
                    'member_notion_id' => $developPoint['member_notion_id'],
                    'in_review_date' => $developPoint['in_review_date']
                ])->first();

                if ($existing) {
                    // レコードが存在する場合、targetを除いて更新
                    $existing->update([
                        'point' => $developPoint['point'],
                        'updated_at' => $developPoint['updated_at'],
                    ]);
                } else {
                    // レコードが存在しない場合、全てのフィールド（targetを含む）で作成
                    $this->developPoint->create($developPoint);
                }
            });
    }

    private function log(string $message): void
    {
        $this->info(CarbonImmutable::now()->format('Y-m-d H:i:s') . ": {$message}");
    }
}
