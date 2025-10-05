<?php

namespace App\Console\Commands;

use App\Models\DevelopPoint;
use App\Models\Member;
use App\Utils\NotionDatabase;
use App\Utils\SlackNotifier;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriodImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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
        $startDate = $this->option('startDate') ?? CarbonImmutable::today()->subMonth();
        $endDate = $this->option('endDate') ?? CarbonImmutable::today();

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

        $this->info(CarbonImmutable::now()->format('Y-m-d H:i:s') . '集計対象日: ' . $inReviewDates->map(fn(CarbonImmutable $date) => $date->format('Y/m/d'))->implode(', '));

        $inReviewDates
            ->each(function (CarbonImmutable $inReviewDate) use ($members) {
                $this->info(CarbonImmutable::now()->format('Y-m-d H:i:s') . ": " . $inReviewDate->format('Y/m/d') . "のデータを取得します");
                $backlogRecords = $this->getBacklogRecords($inReviewDate, $members);
                $this->info("{$backlogRecords->unique('member.notion_id')->count()}人 / {$backlogRecords->count()}件 / {$backlogRecords->sum('point')}ポイント");
                $this->info(CarbonImmutable::now()->format('Y-m-d H:i:s') . ": " . $inReviewDate->format('Y/m/d') . "のデータを集計します");
                $this->aggregateDevelopPoint($inReviewDate, $backlogRecords);
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
        Collection $backlogRecords
    ): void {
        $now = CarbonImmutable::now();
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
            ->each(fn($developPoint) =>

            $this->developPoint->updateOrCreate([
                'member_notion_id' => $developPoint['member_notion_id'],
                'in_review_date' => $developPoint['in_review_date']
            ], $developPoint));
    }
}
