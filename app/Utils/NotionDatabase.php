<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class NotionDatabase
{
    private const NOTION_API_URL = "https://api.notion.com/v1";

    private $headers;
    public $payload;
    public $endpoint;

    public function __construct(string $databaseUrl)
    {
        $this->headers = [
            'Authorization' => "Bearer " . config('notion.api.token'),
            'Notion-Version' => '2022-06-28',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
        ];
        $this->endpoint = self::NOTION_API_URL . "/databases/{$databaseUrl}/query";
    }

    public function setPayload(array $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    public function get(): Collection
    {
        $allResults = collect();
        $hasMore = true;
        $startCursor = null;

        while ($hasMore) {
            if ($startCursor) {
                $this->payload['start_cursor'] = $startCursor;
            }

            $response = Http::withHeaders($this->headers)
                ->post($this->endpoint, $this->payload);

            if ($response->status() >= 300) {
                throw new \Exception('Notion API Error: ' . $response->body());
            }

            $allResults = $allResults->merge($response['results']);
            $hasMore = $response['has_more'] ?? false;
            $startCursor = $response['next_cursor'] ?? null;
        }

        return $allResults;
    }
}
