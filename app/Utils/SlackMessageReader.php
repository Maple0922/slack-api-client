<?php

namespace App\Utils;

use Carbon\CarbonImmutable;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class SlackMessageReader
{
    private const ENDPOINT_CONVERSATIONS_HISTORY = 'https://slack.com/api/conversations.history';
    private const ENDPOINT_CONVERSATIONS_REPLIES = 'https://slack.com/api/conversations.replies';

    private string $channelId;
    private ?CarbonImmutable $start = null;
    private ?CarbonImmutable $end = null;
    private Client $client;
    private string $token;

    public function __construct(string $channelId)
    {
        $this->channelId = $channelId;
        $this->client = new Client();
        $this->token = env('SLACK_TOKEN');

        // デフォルトで今日1日を設定
        $this->setRange(CarbonImmutable::today(), CarbonImmutable::today()->endOfDay());
    }

    /**
     * 時間範囲を設定
     */
    public function setRange(CarbonImmutable $start, CarbonImmutable $end): self
    {
        $this->start = $start;
        $this->end = $end;
        return $this;
    }

    /**
     * メッセージを取得
     */
    public function getMessages(): Collection
    {
        $messages = collect();
        $cursor = null;

        do {
            $params = [
                'channel' => $this->channelId,
                'oldest' => $this->start->timestamp,
                'latest' => $this->end->timestamp,
            ];

            if ($cursor) {
                $params['cursor'] = $cursor;
            }

            $query = http_build_query($params);
            $headers = [
                "Authorization" => "Bearer " . $this->token
            ];

            $requestUrl = self::ENDPOINT_CONVERSATIONS_HISTORY . "?{$query}";
            $response = $this->client->get($requestUrl, ['headers' => $headers]);
            $body = json_decode($response->getBody(), true);

            if (!$body['ok']) {
                throw new \Exception("Slack API エラー: " . ($body['error'] ?? 'Unknown error'));
            }

            $messages = $messages->merge($body['messages'] ?? []);
            $cursor = $body['response_metadata']['next_cursor'] ?? null;
        } while ($cursor);

        return $messages;
    }

    /**
     * スレッドの一覧を取得
     * スレッドの親メッセージ（reply_count > 0）を返す
     */
    public function getThreads(): Collection
    {
        $messages = $this->getMessages();

        // スレッドの親メッセージを取得（reply_count > 0 かつ thread_ts が存在しない、または thread_ts === ts）
        $threads = $messages->filter(function ($message) {
            // スレッドの親メッセージは reply_count > 0 で、thread_ts が存在しないか、ts と同じ
            if (isset($message['reply_count']) && $message['reply_count'] > 0) {
                return !isset($message['thread_ts']) || $message['thread_ts'] === $message['ts'];
            }
            return false;
        });

        return $threads;
    }

    /**
     * 特定のメッセージのスレッド返信を取得
     */
    public function getThreadReplies(string $threadTs): Collection
    {
        $params = [
            'channel' => $this->channelId,
            'ts' => $threadTs,
        ];

        $query = http_build_query($params);
        $headers = [
            "Authorization" => "Bearer " . $this->token
        ];

        $requestUrl = self::ENDPOINT_CONVERSATIONS_REPLIES . "?{$query}";
        $response = $this->client->get($requestUrl, ['headers' => $headers]);
        $body = json_decode($response->getBody(), true);

        if (!$body['ok']) {
            throw new \Exception("Slack API エラー: " . ($body['error'] ?? 'Unknown error'));
        }

        return collect($body['messages'] ?? []);
    }
}
