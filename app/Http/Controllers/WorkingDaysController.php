<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\OffDate;
use App\Models\Member;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriodImmutable;
use Illuminate\Http\Request;

class WorkingDaysController extends Controller
{
    public function index(int $monthOffset): array
    {
        $startDate = CarbonImmutable::now()->startOfMonth()->addMonths($monthOffset);
        $endDate = $startDate->endOfMonth();

        $members = Member::select([
            'notion_id',
            'name',
            'image_url',
            'target_point',
        ])
            ->with('offDates')
            ->where('is_valid', 1)
            ->get();

        $period = CarbonPeriodImmutable::create($startDate, $endDate);

        $workingDays = collect($period)
            ->map(fn($date) => [
                'date' => $date->format('Y-m-d'),
                'week' => $date->format('D'),
                'isSaturday' => $date->isSaturday(),
                'isSunday' => $date->isSunday(),
                'members' => $members
                    ->filter(fn($member) =>
                    !$date->isWeekEnd() && $member->offDates->doesntContain(
                        fn($offDate) =>
                        $offDate->date->isSameDay($date)
                    ))
                    ->map(fn($member) => [
                        'id' => $member->notion_id,
                        'name' => $member->name
                    ])
                    ->values()
            ]);

        return [
            'month' => $startDate->format('Y-m'),
            'workingDays' => $workingDays->toArray(),
        ];
    }

    public function create(Request $request)
    {
        $date = CarbonImmutable::parse($request->input('date'));
        $memberId = $request->input('memberId');

        OffDate::where('member_notion_id', $memberId)
            ->whereDate('date', $date)
            ->delete();

        return response()->json([
            'message' => 'Off date removed successfully.',
        ]);
    }

    public function delete(string $date, string $memberId)
    {
        OffDate::updateOrCreate(
            [
                'member_notion_id' => $memberId,
                'date' => $date,
            ],
            []
        );

        return response()->json([
            'message' => 'Off date removed successfully.',
        ]);
    }
}
