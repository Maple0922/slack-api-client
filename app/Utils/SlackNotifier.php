<?php

namespace App\Utils;

use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class SlackNotifier extends Command
{
    public string $webhookUrl;
    public string $appName = 'Slack Bot';
    public string $message = 'メッセージです';
    public string $iconEmoji = ':slack:';

    public function __construct(string $webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function setAppName(string $appName)
    {
        $this->appName = $appName;
        return $this;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    public function setIconEmoji(string $iconEmoji)
    {
        $this->iconEmoji = $iconEmoji;
        return $this;
    }

    public function send(): Response
    {
        $data = [
            'text' => $this->message,
            'username' => $this->appName,
            'icon_emoji' => $this->iconEmoji,
        ];

        $response = Http::post($this->webhookUrl, $data);

        if ($response->failed()) {
            throw new \Exception("Slack通知に失敗しました。{$response->getBody()}");
        }

        return $response;
    }
}
