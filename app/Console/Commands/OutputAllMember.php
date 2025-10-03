<?php

namespace App\Console\Commands;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OutputAllMember extends Command
{
    protected $signature = 'slack:outputAllMember';

    protected $description = '全メンバーのユーザー名とNotion IDを出力する';

    public function __construct(
        private Member $member
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $members = $this->getBacklogRecordDetails();

        $this->info('全メンバーのユーザー名とNotion ID:');
        $this->table(
            ['ユーザー名', 'Notion ID'],
            $members
                ->map(fn($member) => [
                    $member['name'],
                    $member['notion_id']
                ])
        );
    }

    private function getBacklogRecordDetails()
    {
        $notionApiUrl = config('notion.api.url');
        $backlogDatabaseUrl = config('notion.api.backlogDatabaseUrl');
        $parentDatabaseUrl = config('notion.api.parentDatabaseUrl');
        $notionToken = config('notion.api.token');

        $backlogEndpoint = "{$notionApiUrl}/databases/{$backlogDatabaseUrl}/query";
        $parentEndPoint = "{$notionApiUrl}/databases/{$parentDatabaseUrl}/query";

        $headers = [
            'Authorization' => "Bearer {$notionToken}",
            'Notion-Version' => '2022-06-28',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
        ];

        // Backlog数値管理から、当日を含めた次の火曜日のページIDを取得
        $nextTuesday = Carbon::now()->subHours(36)->next(Carbon::TUESDAY)->format('Y/m/d');
        $parentPayload = config('notion.payload.parent');
        $parentPayload['filter']['rich_text']['equals'] = $nextTuesday;
        $parentResponse = Http::withHeaders($headers)->post($parentEndPoint, $parentPayload);
        $parentPageId = $parentResponse['results'][0]['id'];

        // Backlogから、次の火曜日のページIDの子ページを取得（ページネーション対応）
        $allResults = collect();
        $startCursor = null;
        $hasMore = true;

        while ($hasMore) {
            $backlogPayload = config('notion.payload.backlog.progress');
            $backlogPayload['filter']['and'][0]['relation']['contains'] = $parentPageId;

            // ページネーション用のstart_cursorを設定
            if ($startCursor) {
                $backlogPayload['start_cursor'] = $startCursor;
            }

            $backlogResponse = Http::withHeaders($headers)->post($backlogEndpoint, $backlogPayload);

            // 結果を追加
            $allResults = $allResults->merge($backlogResponse['results']);

            // 次のページがあるかチェック
            $hasMore = $backlogResponse['has_more'] ?? false;
            $startCursor = $backlogResponse['next_cursor'] ?? null;
        }

        return $allResults
            ->map(fn($result) => $result['properties']['Manager']['people'][0]['id'])
            ->unique()
            ->map(fn($notionId) => [
                'notion_id' => $notionId,
                'name' => $this->member->where('notion_id', $notionId)->value('name') ?? '不明'
            ])
            ->values();
    }
}
