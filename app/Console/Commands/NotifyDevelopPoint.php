<?php

namespace App\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class NotifyDevelopPoint extends Command
{
    protected $signature = 'slack:notifyDevelopPoint';

    protected $description = '開発ポイントの進捗をSlack通知する';

    public function handle()
    {
        $url = config('slack.webhook.notifyTest');
        $contents = config('slack.template.main');

        $members = config('members');

        $memberPointPayloads = collect($members)
            ->flatMap($this->generateMemberPointPayload())
            ->toArray();

        array_splice($contents['blocks'], 2, 0, $memberPointPayloads);
        $headers = ['Content-type' => 'application/json'];

        $response = Http::withHeaders($headers)->post($url, $contents);

        if ($response->status() >= 300) {
            $failedContents = config('slack.template.failed');
            Http::withHeaders($headers)->post($url, $failedContents);
        }
    }

    private function generateMemberPointPayload(): Closure
    {
        return function ($member): array {
            $memberTemplate = json_encode(config('slack.template.member'));

            [
                $name,
                $imageUrl,
                $totalPoint,
                $donePoint,
                $targetPoint
            ] = [
                $member['name'],
                $member['imageUrl'],
                $member['totalPoint'],
                $member['donePoint'],
                $member['targetPoint']
            ];
            $doneRate = round($donePoint / $totalPoint, 2);
            $targetRate = round($donePoint / $targetPoint, 2);
            $doneRateCount = floor($doneRate * 10);
            $doneRateStars = str_repeat(':star:', $doneRateCount) . str_repeat(':black_star:', 10 - $doneRateCount);

            $replace = [
                '%memberName%' => $name,
                '%imageUrl%' => $imageUrl,
                '%totalPoint%' => $totalPoint,
                '%targetPoint%' => $targetPoint,
                '%donePoint%' => $donePoint,
                '%targetRate%' => $targetRate * 100 . "%",
                '%doneRate%' => $doneRate * 100 . "%",
                '%doneRateStars%' => $doneRateStars,
                '%eol%' => '\n',
            ];

            return json_decode(strtr($memberTemplate, $replace), true);
        };
    }
}
