<?php
declare(strict_types=1);

namespace App\Infrastructure\Vk;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class VkApiService
{
    public function __construct(
        private readonly Client $httpClient,
        private readonly string $botToken,
        private readonly string $version,
    )
    {
    }

    public function sendMessage(int $peerId, string $text): void
    {
        for ($i = 0; $i <= mb_strlen($text); $i += 4096) {
            $bodyParams = [
                'random_id' => 0,
                'peer_id' => $peerId,
                'message' => mb_substr($text, $i, 4096),
                'v' => $this->version,
            ];
            $request = new Request(
                method: 'POST',
                uri: 'https://api.vk.com/method/messages.send?' . http_build_query($bodyParams),
                headers: [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->botToken
                ],
                body: json_encode($bodyParams, 256)
            );
            $this->httpClient->sendRequest($request);
        }
    }
}