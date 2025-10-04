<?php

namespace App\Http\Controllers;

use App\Models\DevelopPoint;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class DevelopPointController extends Controller
{
    public function __construct(
        private DevelopPoint $developPoint
    ) {}

    public function index(Request $request)
    {
        $monthOffset = (int)$request->monthOffset ?? 0;
        $dateRange = $this->calcQuarterDateRange($monthOffset);

        $developPoints = $this->developPoint
            ->with('member.team')
            ->whereBetween('in_review_date', $dateRange)
            ->get()
            ->groupBy('in_review_date')
            ->map(fn($developPoints) => [
                'inReviewDate' => $developPoints->first()->in_review_date->format('Y-m-d'),
                'members' => $developPoints->map(fn($developPoint) => [
                    'notionId' => $developPoint->member_notion_id,
                    'slackId' => $developPoint->member->slack_id,
                    'name' => $developPoint->member->name,
                    'imageUrl' => $developPoint->member->image_url,
                    'team' => [
                        'id' => $developPoint->member->team_id ?? null,
                        'name' => $developPoint->member->team->name ?? null,
                    ],
                    'point' => $developPoint->point,
                    'target' => $developPoint->target,
                ]),
            ])
            ->values();

        return [
            'dateRange' => [
                'start' => $dateRange[0]->format('Y/m/d'),
                'end' => $dateRange[1]->format('Y/m/d'),
            ],
            'points' => $developPoints,
        ];
    }

    private function calcQuarterDateRange(int $monthOffset)
    {
        $now = CarbonImmutable::today();
        $targetQuarter = $now->addMonths($monthOffset);
        $targetMonth = $targetQuarter->month;

        return match ($targetMonth) {
            1, 2, 3 => [
                $targetQuarter
                    ->setYear($targetQuarter->year - 1)
                    ->setMonth(10)
                    ->setDay(1),
                $targetQuarter
                    ->setMonth(3)
                    ->setDay(31)
            ],
            4, 5, 6, 7, 8, 9 => [
                $targetQuarter
                    ->setMonth(4)
                    ->setDay(1),
                $targetQuarter
                    ->setMonth(9)
                    ->setDay(30)
            ],
            10, 11, 12 => [
                $targetQuarter
                    ->setMonth(10)
                    ->setDay(1),
                $targetQuarter
                    ->setYear($targetQuarter->year + 1)
                    ->setMonth(3)
                    ->setDay(31)
            ],
        };
    }
}
