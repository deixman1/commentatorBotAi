<?php
declare(strict_types=1);

namespace App\Infrastructure\Telegram;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class TelegramApiService
{
    public function __construct(
        private readonly Client $httpClient,
        private readonly string $botToken,
    )
    {
    }

    public function sendMessage(int $chatId, string $text, $parseMode = 'HTML'): void
    {
        for ($i = 0; $i <= mb_strlen($text); $i += 4096) {
            $bodyParams = [
                'chat_id' => $chatId,
                'text' => mb_substr($text, $i, 4096),
                'parse_mode' => $parseMode,
            ];
            $request = new Request(
                method: 'POST',
                uri: 'https://api.telegram.org/bot' . $this->botToken . '/sendMessage',
                headers: ['Content-Type' => 'application/json'],
                body: json_encode($bodyParams, 256)
            );
            $this->httpClient->sendRequest($request);
        }
    }
}